build:
	make repo
	make satis

install:
	./composer.phar install

repo:
	./bin/console pear-satis:update ${GITHUB_OAUTH_TOKEN} pear pear2

satis:
	./vendor/bin/satis build --skip-errors
	./bin/console pear-satis:upload pear.imagineeasy.com ${AWS_ACCESS_KEY_ID} ${AWS_SECRET_ACCESS_KEY}

clean:
	rm -rf vendor/
	rm -rf www/
	rm -r satis.json
