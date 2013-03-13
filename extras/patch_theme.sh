#!/usr/bin/env sh
THEME="${1}";
LAST="${LAST:-$(cat extras/last_defaut_theme_changes)}";
shift 1;

git diff "${LAST}" "${LAST}~1" -- catalog/view/theme/default/ | \
sed -e 's#theme/default#theme/'"${1}"'#g' > "THEME_${THEME}.patch"

# | patch -p1 -ElN --no-backup-if-mismatch
