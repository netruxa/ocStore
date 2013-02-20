#!/usr/bin/env bash

function find_missing_files() {
find admin/language/english -type f | while read f; do
	FILE=${f//english/russian};
	test -f "${FILE}" || (
		echo "Отсутствует: ${FILE}. Скопировано из английского перевода.";
	#	cp ${f} ${FILE}
	)
done
}

function find_missing_strings() {
find admin/language/english -type f | while read f; do
	FILE="${f//english/russian}";
	test -f "${FILE}" || { echo "Фатальная ошибка!"; exit 1; }
	grep \\$ "${f}" | \
	sed -r -e 's/\$_\[(.*)\].*/\1/' | \
	while read i; do
		grep "${i}" "${FILE}" >/dev/null || (
			BEFORE="$(grep -B1 ${i} ${f}|sed -r -e 's/\$_\[(.*)\].*/\1/' -e '/'${i}'/d')";
			AFTER="$(grep -A1 ${i} ${f}|sed -r -e 's/\$_\[(.*)\].*/\1/' -e '/'${i}'/d')";

			echo "Нету: ${i}; Находится между ${BEFORE} и ${AFTER}; Строка скопирована из английского перевода и требует вмешательства.";
			grep ${i} ${f} | while read tr; do sed -r -e "s@(.*${BEFORE}.*)@\1\n${tr}@" -i ${FILE}; done;
		)
	done;
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

find_missing_files;
find_missing_strings;
find_untranslated_strings;
