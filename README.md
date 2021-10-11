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