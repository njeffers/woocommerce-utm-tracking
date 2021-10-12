# WooCommerce UTM Tracking

[![Build status][build-status]][travis-ci]

This plugin allows for basic UTM Tracking and viewing in WooCommerce Orders.

![alt text](utm.jpg)

Default UTM's tracked:
- utm_source
- utm_campaign
- utm_content
- utm_medium
- utm_term

These can be modified via the `get_tracking_meta_keys` filter.


## Set up

1. Copy into `/wp-content/plugins/`

2. Make sure WooCommerce is activated before activating this plugin.

[build-status]: https://app.travis-ci.com/njeffers/woocommerce-utm-tracking.svg?branch=main
[travis-ci]: https://app.travis-ci.com/njeffers/woocommerce-utm-tracking



# Docker
### Start Environment
```sh
docker-compose up -d
```

### Install WordPress

```sh
docker-compose run --rm wp-cli install-wp
```

### Install Tests
```sh
docker-compose -f docker-compose.yml -f docker-compose.phpunit.yml up -d
docker-compose -f docker-compose.phpunit.yml run --rm wordpress_phpunit /app/bin/install-wp-tests.sh wordpress_test root '' mysql_phpunit latest true
```

### Run Tests
```sh
docker-compose -f docker-compose.phpunit.yml run --rm wordpress_phpunit phpunit
```

---

### ISSUES

```
/usr/local/bin/docker-entrypoint.sh: exec: line 15: install-wp: Permission denied
```

```
chmod +x install-wp.sh
```


```
Error response from daemon: OCI runtime create failed: container_linux.go:380: starting container process caused: exec: "/app/bin/install-wp-tests.sh": permission denied: unknown
```
```
chmod +x install-wp-tests.sh
```