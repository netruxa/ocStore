#!/usr/bin/env bash

function find_missing_files() {
echo
}

function find_missing_strings() {
grep \\$ admin/language/english/english.php | \
sed -r -e 's/\$_\[(.*)\].*/\1/' | \
while read i; do
	grep "${i}" admin/language/russian/russian.php >/dev/null || \
	echo "Нету: ${i}; Находится между \
$(grep -B1 ${i} admin/language/english/english.php|sed -r -e 's/\$_\[(.*)\].*/\1/' -e '/'${i}'/d') \
и \
$(grep -A1 ${i} admin/language/english/english.php|sed -r -e 's/\$_\[(.*)\].*/\1/' -e '/'${i}'/d')";
done;
}

function find_untranslated_strings() {
grep -r \\$ ./admin/language/russian | grep -v '[а-яА-Я]' | while read F;
do
	FILE="$(echo ${F} | awk -F: '{print $1}')"
	STRING="${F//${FILE}:}"
	echo "Строка «${STRING}» в файле «${FILE}» требует перевода!"
done;
}

find_missing_strings;
#find_untranslated_strings;
