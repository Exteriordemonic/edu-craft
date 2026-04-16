#!/usr/bin/env bash
# Deploy edu-craft-theme to MyDevil (rsync over SSH).
# Pełny kod z gita na serwerze: scripts/sync-repo-on-remote.sh
# Copy deploy/db.*.env.example → deploy/db.*.env for DB reference (not used by this script).

set -euo pipefail

# --- Remote SSH (edit if your panel uses another host/user) ---
REMOTE_USER="${REMOTE_USER:-Exteriordemonic}"
REMOTE_HOST="${REMOTE_HOST:-s24.mydevil.net}"
REMOTE_DOMAIN="${REMOTE_DOMAIN:-hc-edu-craft.miroszdevelopment.pl}"
SSH_PORT="${SSH_PORT:-22}"

# Path on server: DDEV-style docroot under public_html
REMOTE_THEME_PATH="${REMOTE_THEME_PATH:-~/domains/${REMOTE_DOMAIN}/public_html/web/wp-content/themes/edu-craft-theme/}"

# Set DEPLOY_DRY_RUN=1 to print what would be synced without writing.
DEPLOY_DRY_RUN="${DEPLOY_DRY_RUN:-0}"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
LOCAL_THEME="${REPO_ROOT}/web/wp-content/themes/edu-craft-theme/"

if [[ ! -d "${LOCAL_THEME}" ]]; then
	echo "error: local theme directory missing: ${LOCAL_THEME}" >&2
	exit 1
fi

RSYNC_OPTS=(-avz --delete)
RSYNC_OPTS+=(--exclude node_modules)
RSYNC_OPTS+=(--exclude .git)
RSYNC_OPTS+=(--exclude .DS_Store)
RSYNC_OPTS+=(--exclude .ddev)

if [[ "${DEPLOY_DRY_RUN}" == "1" ]]; then
	RSYNC_OPTS+=(-n)
	echo "Dry run (no changes on server). Unset DEPLOY_DRY_RUN or set to 0 for a real deploy."
fi

DEST="${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_THEME_PATH}"
SSH_CMD="ssh -p ${SSH_PORT}"

echo "Deploying edu-craft-theme → ${DEST}"

rsync "${RSYNC_OPTS[@]}" -e "${SSH_CMD}" \
	"${LOCAL_THEME}" \
	"${DEST}"

echo "Deploy finished."
