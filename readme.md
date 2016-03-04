PHP built-in INTL functions demo program
=====================================================================

This demo program plays with PHP's Locale and NumberFormatter classes.

This demo program also includes the ISO-3166 database from
[alcohol](http://alcohol.github.io/iso3166/)


Examples
---------------------------------------------------------------------
```bash
php locale.php -l zh-hans -v -d zh-hant -GTVCRD
```

```bash
php formatter.php -v $9876543210.012345 -ALPSTu -a GROUPING_SEPARATOR_SYMBOL=\'
```

```bash
php iso3166.php -l3
```
