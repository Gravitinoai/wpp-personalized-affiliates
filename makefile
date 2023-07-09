all:
	composer install  --working-dir src/
	zip -r pers_content_plugin.zip src/
