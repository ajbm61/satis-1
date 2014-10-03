build:
	./bin/console pear-satis:update pear ${GITHUB_OAUTH_TOKEN}
	./vendor/bin/satis build --skip-errors
	./bin/console pear-satis:upload pear.imagineeasy.com ${AWS_ACCESS_KEY_ID} ${AWS_SECRET_ACCESS_KEY}

install:
	./composer.phar install

clean:
	rm -rf vendor/
	rm -rf www/
	rm -r satis.json
