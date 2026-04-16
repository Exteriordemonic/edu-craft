#!/usr/bin/env bash
# Synchronizacja kodu na serwerze przez git (pierwsze uruchomienie: clone, potem: fetch + checkout + pull).
# Baza i wp-config na produkcji — osobno (import SQL, panel).
#
# Wymaga: SSH do MyDevil; na serwerze git oraz dostęp do origin (deploy key / token przy HTTPS).
#
# Docroot: w panelu wskaż .../repos/edu-craft/web albo symlink public_html/web → ten katalog web.

set -euo pipefail

REMOTE_USER="${REMOTE_USER:-Exteriordemonic}"
REMOTE_HOST="${REMOTE_HOST:-s24.mydevil.net}"
SSH_PORT="${SSH_PORT:-22}"
REMOTE_DOMAIN="${REMOTE_DOMAIN:-hc-edu-craft.miroszdevelopment.pl}"
REMOTE_REPO_NAME="${REMOTE_REPO_NAME:-edu-craft}"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"

# Ref do checkoutu na serwerze: domyślnie upstream bieżącego brancha (np. main → master),
# bo na origin często nie ma lokalnej nazwy brancha (brak origin/main).
if [[ -z "${GIT_REF:-}" ]]; then
	_upstream="$(git -C "${REPO_ROOT}" rev-parse --abbrev-ref '@{u}' 2>/dev/null || true)"
	if [[ -n "${_upstream}" ]]; then
		GIT_REF="${_upstream#origin/}"
	else
		GIT_REF="$(git -C "${REPO_ROOT}" branch --show-current)"
	fi
	unset _upstream
fi
GIT_REMOTE_URL="${GIT_REMOTE_URL:-$(git -C "${REPO_ROOT}" remote get-url origin)}"

SSH_CMD=(ssh -p "${SSH_PORT}" "${REMOTE_USER}@${REMOTE_HOST}")

echo "Domena (ścieżka na serwerze): domains/${REMOTE_DOMAIN}/repos"
echo "Katalog repozytorium: ${REMOTE_REPO_NAME}"
echo "Branch: ${GIT_REF}"
echo "Remote URL: ${GIT_REMOTE_URL}"
echo

"${SSH_CMD[@]}" \
	env \
	DOMAIN="${REMOTE_DOMAIN}" \
	GIT_REF="${GIT_REF}" \
	GIT_REMOTE_URL="${GIT_REMOTE_URL}" \
	REPO_DIR_NAME="${REMOTE_REPO_NAME}" \
	bash -s <<'ENDSSH'
set -euo pipefail
rep_root="${HOME}/domains/${DOMAIN}/repos"
mkdir -p "${rep_root}"
cd "${rep_root}"

if [[ ! -d "${REPO_DIR_NAME}/.git" ]]; then
	echo "Klonowanie..."
	git clone "${GIT_REMOTE_URL}" "${REPO_DIR_NAME}"
fi

cd "${REPO_DIR_NAME}"
git remote set-url origin "${GIT_REMOTE_URL}" 2>/dev/null || true
git fetch origin
git checkout "${GIT_REF}"
# Pull tylko na gałęzi (przy checkoutcie tagu HEAD jest detached).
if git symbolic-ref -q HEAD >/dev/null 2>&1; then
	git pull --ff-only origin "${GIT_REF}"
fi

echo
echo "OK. WordPress (docroot): $(pwd)/web"
ENDSSH

echo
echo "Dalej: import bazy, produkcyjny wp-config, ewentualnie npm run build w motywie jeśli dist nie jest w repozytorium."
