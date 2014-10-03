build:
	./bin/console pear-satis:update pear ${GITHUB_OAUTH_TOKEN}
	./vendor/bin/satis build --skip-errors

install:
	./composer.phar install

clean:
	rm -rf vendor/
	rm -rf www/
	rm -r satis.json
