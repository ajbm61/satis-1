# A composer repository for PEAR

## Usage

To use this composer repository, please go to [http://pear.imagineeasy.com/](http://pear.imagineeasy.com) and follow the instructions!

## Behind the scenes

 * the (generated) repository is hosted on a static website on Amazon S3
 * the job to update the repository is run on a (free) dyno on [heroku](http://www.heroku.com/)
 * the Amazon bill is taken care of by [Imagine Easy Solutions LLC](http://www.imagineeasy.com)

## Contributing

### Install

```sh
$ make install
```

### Configuration

All the commands accept parameters, but it is probably easier when you export these:

 * `GITHUB_OAUTH_TOKEN`
 * `AWS_ACCESS_KEY_ID`
 * `AWS_SECRET_ACCESS_KEY`

### Update / Build repository

```sh
$ make build
```

## Maintainer

Currently maintained by [Till Klamp&auml;ckel](http://twitter.com/klimpong).