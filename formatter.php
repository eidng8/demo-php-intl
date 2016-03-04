#!/usr/bin/env php
<?php
/**
 * Project:  Locale
 * File:     formatter.php
 * Created:  2016-03-03
 *
 * PHP Version >=5.5.9
 *
 * @package   Tpages\Default
 * @author    Tpages <master@tpages.com>
 * @copyright 2013-2014 Guangzhou Tianao Internet Technology Limited.
 *            (c) 广州市天傲互联网科技有限公司。版权所有。
 * @license   http://www.tpages.com/license Tpages License and Agreement
 *            Any person receiving this material, shall treat it as confidential
 *            and not misuse, copy, disclose, distribute or retain the material
 *           in any way that amounts to a breach of confidentiality.
 *           任何收到该资料的人员，都必须对该资料进行保密。不得通过任何可能损害其保密性的方
 *           法或途径进行保留、复制、披露、及颁发。
 * @link      http://www.tpages.com
 */

$shortopts = 'puALPSTa:c:d:l:n:o:s:t:v:';
$longopts = [
  'parse', 'parse-currency', 'get-attribute', 'get-locale', 'get-pattern',
  'get-symbol', 'get-text-attribute', 'attribute:', 'currency:',
  'display-locale:', 'locale:', 'pattern:', 'offset:', 'style:', 'type:',
  'value:'
];

if(!($opts = getopt($shortopts, $longopts)) || empty($opts['v'])
)
{
  die(
    <<<EOT

formatter.php <-v value>


Format a given number. All arguements are optional except -v.
Please check PHP document when using -a, -c, -d, -l, -n, -o.


-p, --parse               Parses -v, according to -t.
-u, --parse-currency      Parses -v as currency.
-A, --get-attribute       Shows formatting attributes.
-L, --get-locale          Shows display locale.
-P, --get-pattern         Shows formatting pattern.
-S, --get-symbol          Shows currency symbol.
-T, --get-text-attribute  Shows text attributes.
-a, --attribute           Sets formatting attributes.
-c, --currency            Sets which currency to use.
-d, --display-locale      Sets display locale.
-l, --locale              Sets input locale.
-n, --pattern             Sets formatting pattern.
-o, --offset              Sets the starting offset for -p and -u.

-s, --style               Sets formatting style. Can be one of:
                          pat: PATTERN_DECIMAL
                          dec: DECIMAL
                          cur: CURRENCY
                          per: PERCENT
                          sci: SCIENTIFIC
                          spe: SPELLOUT
                          ord: ORDINAL
                          dur: DURATION
                          rul: PATTERN_RULEBASED

-t, --type                Sets data type. Can be one of:
                          32: TYPE_INT32
                          64: TYPE_INT64
                          f:  TYPE_DOUBLE
                          c:  TYPE_CURRENCY

-v, --value               Sets the input value.


Example:

./formatter.php -v $9876543210.012345 -ALPSTu -a GROUPING_SEPARATOR_SYMBOL=\'


EOT
  );
}//end if

require_once 'func.php';

$opts = matchArguements($opts, $shortopts, $longopts);

//////////////////////////////////////////////////////////////////////////
/**
 * Number format symbol constants
 */
$SYMBOLS = [
  '::DECIMAL_SEPARATOR_SYMBOL',
  '::GROUPING_SEPARATOR_SYMBOL',
  '::PATTERN_SEPARATOR_SYMBOL',
  '::PERCENT_SYMBOL',
  '::ZERO_DIGIT_SYMBOL',
  '::DIGIT_SYMBOL',
  '::MINUS_SIGN_SYMBOL',
  '::PLUS_SIGN_SYMBOL',
  '::CURRENCY_SYMBOL',
  '::INTL_CURRENCY_SYMBOL',
  '::MONETARY_SEPARATOR_SYMBOL',
  '::EXPONENTIAL_SYMBOL',
  '::PERMILL_SYMBOL',
  '::PAD_ESCAPE_SYMBOL',
  '::INFINITY_SYMBOL',
  '::NAN_SYMBOL',
  '::SIGNIFICANT_DIGIT_SYMBOL',
  '::MONETARY_GROUPING_SEPARATOR_SYMBOL'
];
if(!sort($SYMBOLS, SORT_STRING))
{
  die('Failed to sort $SYMBOLS' . PHP_EOL);
}

/**
 * Number format attribute constants
 */
$ATTRIBUTES = [
  '::PARSE_INT_ONLY',
  '::GROUPING_USED',
  '::DECIMAL_ALWAYS_SHOWN',
  '::MAX_INTEGER_DIGITS',
  '::MIN_INTEGER_DIGITS',
  '::INTEGER_DIGITS',
  '::MAX_FRACTION_DIGITS',
  '::MIN_FRACTION_DIGITS',
  '::FRACTION_DIGITS',
  '::MULTIPLIER',
  '::GROUPING_SIZE',
  '::ROUNDING_MODE',
  '::ROUNDING_INCREMENT',
  '::FORMAT_WIDTH',
  '::PADDING_POSITION',
  '::SECONDARY_GROUPING_SIZE',
  '::SIGNIFICANT_DIGITS_USED',
  '::MIN_SIGNIFICANT_DIGITS',
  '::MAX_SIGNIFICANT_DIGITS',
  '::LENIENT_PARSE'
];
if(!sort($ATTRIBUTES, SORT_STRING))
{
  die('Failed to sort $ATTRIBUTES' . PHP_EOL);
}

/**
 * Rounding mode constants
 */
$ROUNDINGS = [
  '::ROUND_CEILING',
  '::ROUND_DOWN',
  '::ROUND_FLOOR',
  '::ROUND_HALFDOWN',
  '::ROUND_HALFEVEN',
  '::ROUND_HALFUP',
  '::ROUND_UP'
];
if(!sort($ROUNDINGS, SORT_STRING))
{
  die('Failed to sort $ROUNDINGS' . PHP_EOL);
}

/**
 * Pad position constants
 */
$PADDINGS = [
  '::PAD_AFTER_PREFIX',
  '::PAD_AFTER_SUFFIX',
  '::PAD_BEFORE_PREFIX',
  '::PAD_BEFORE_SUFFIX'
];
if(!sort($PADDINGS, SORT_STRING))
{
  die('Failed to sort $PADDINGS' . PHP_EOL);
}

/**
 * Number format text attribute constants
 */
$TEXTATTRIBUTES = [
  '::POSITIVE_PREFIX',
  '::POSITIVE_SUFFIX',
  '::NEGATIVE_PREFIX',
  '::NEGATIVE_SUFFIX',
  '::PADDING_CHARACTER',
  '::CURRENCY_CODE',
  '::DEFAULT_RULESET',
  '::PUBLIC_RULESETS',
];
if(!sort($TEXTATTRIBUTES, SORT_STRING))
{
  die('Failed to sort $TEXTATTRIBUTES' . PHP_EOL);
}

//////////////////////////////////////////////////////////////////////////
/*
 * --locale
 */
$inputLocale = empty($opts['l']) ? Locale::getDefault() : $opts['l'];

/*
 * --display-locale
 */
$displayLocale = empty($opts['d']) ? Locale::getDefault() : $opts['d'];

/*
 * --value
 */
$value = $opts['v'];

/*
 * --style
 */
$style = getStyle($opts);

/*
 * --type
 */
$valueType = getValueType($opts);

/*
 * --offset
 */
$offset = empty($opts['o']) ? 0 : $opts['o'];

echo "$inputLocale => $displayLocale (input => display)" . PHP_EOL . PHP_EOL;

/*
 * --parse-currency
 */
if(isset($opts['u']))
{
  echo '--parse-currency is specified, forcing to use ::CURRENCY style' .
       PHP_EOL . PHP_EOL;
  $style = NumberFormatter::CURRENCY;
  $input = new NumberFormatter($inputLocale, $style);
  $start = $offset;
  $value = $input->parseCurrency($value, $currency, $offset);
  printValue(
    "$value in $currency (end at $offset)", "->parseCurrency(position=$start)"
  );
  $opts['c'] = $currency;
}

/*
 * --parse
 */
elseif(isset($opts['p']))
{
  $input = new NumberFormatter($inputLocale, $style);
  $start = $offset;
  if(NumberFormatter::TYPE_DEFAULT == $valueType)
  {
    $valueType = NumberFormatter::TYPE_DOUBLE;
  }

  $value = $input->parse($value, $valueType, $offset);
  printValue(
    "$value (end at $offset)", "->parse(position=$start)"
  );
}

$formatter = new NumberFormatter($displayLocale, $style);

/*
 * --attribute
 */
if(!empty($opts['a']))
{
  $attrs = $opts['a'];

  if(!is_array($attrs))
  {
    $attrs = [$attrs];
  }

  foreach($attrs as $attr)
  {
    $key = explode('=', $attr, 2);
    if(count($key) != 2 || empty($key[1]))
    {
      die("Please provide a value to attribute $attr" . PHP_EOL);
    }

    list($key, $val) = $key;
    $key = '::' . strtoupper($key);
    if(false !== array_search($key, $ATTRIBUTES))
    {
      $method = 'setAttribute';
    }
    elseif(false !== array_search($key, $ROUNDINGS))
    {
      $method = 'setAttribute';
    }
    elseif(false !== array_search($key, $PADDINGS))
    {
      $method = 'setAttribute';
    }
    elseif(false !== array_search($key, $SYMBOLS))
    {
      $method = 'setSymbol';
    }
    elseif(false !== array_search($key, $TEXTATTRIBUTES))
    {
      $method = 'setTextAttribute';
    }
    else
    {
      die("Invalid attribute: $attr" . PHP_EOL);
    }//end if

    $ret = $formatter->$method(constant("NumberFormatter$key"), $val);
    if($ret)
    {
      printValue("$key => $val", "->$method()");
    }
    else
    {
      printValue($ret, "->$method($key, $val)");
    }
  }//end foreach
}//end if

/*
 * --pattern
 */
if(!empty($opts['n']))
{
  if(NumberFormatter::PATTERN_RULEBASED == $style)
  {
    echo '--pattern is ignored for rule-based style';
  }
  else
  {
    $formatter->setPattern($opts['n']);
  }
}

/*
 * --currency
 */
if(!empty($opts['c']))
{
  printValue(
    $formatter->formatCurrency($value, $opts['c']), '->formatCurrency()'
  );
}

/*
 * --value
 */
printValue($formatter->format($opts['v'], $valueType), '->format()');

/*
 * --get-locale
 */
if(isset($opts['d']))
{
  echo '->getLocale():' . PHP_EOL;
  printValue(
    $formatter->getLocale(Locale::VALID_LOCALE),
    '  \Locale::VALID_LOCALE'
  );
  printValue(
    $formatter->getLocale(Locale::ACTUAL_LOCALE),
    '  \Locale::ACTUAL_LOCALE'
  );
}

/*
 * --get-pattern
 */
if(isset($opts['P']))
{
  printValue($formatter->getPattern(), '->getPattern()');
}

/*
 * --get-symbol
 */
if(isset($opts['S']))
{
  echo '->getSymbol():' . PHP_EOL;
  foreach($SYMBOLS as $symbol)
  {
    printValue(
      $formatter->getSymbol(constant("NumberFormatter$symbol")), "  $symbol"
    );
  }
}//end if

/*
 * --get-attribute
 */
if(isset($opts['A']))
{
  echo '->getAttribute():' . PHP_EOL;
  foreach($ATTRIBUTES as $attr)
  {
    printValue(
      $formatter->getAttribute(constant("NumberFormatter$attr")), "  $attr"
    );
  }

  foreach($ROUNDINGS as $attr)
  {
    printValue(
      $formatter->getAttribute(constant("NumberFormatter$attr")), "  $attr"
    );
  }

  foreach($PADDINGS as $attr)
  {
    printValue(
      $formatter->getAttribute(constant("NumberFormatter$attr")), "  $attr"
    );
  }
}//end if

/*
 * --get-text-attribute
 */
if(isset($opts['T']))
{
  echo '->getTextAttribute(():' . PHP_EOL;
  if(NumberFormatter::PATTERN_RULEBASED != $style)
  {
    printValue(
      'Ignored. Please use a rule-based style to enable this attribute.',
      '  ::DEFAULT_RULESET'
    );
    printValue(
      'Ignored. Please use a rule-based style to enable this attribute.',
      '  ::PUBLIC_RULESETS'
    );
    unset(
      $TEXTATTRIBUTES[array_search('::DEFAULT_RULESET', $TEXTATTRIBUTES)],
      $TEXTATTRIBUTES[array_search('::PUBLIC_RULESETS', $TEXTATTRIBUTES)]
    );
  }

  foreach($TEXTATTRIBUTES as $attr)
  {
    printValue(
      $formatter->getTextAttribute(constant("NumberFormatter$attr")),
      "  $attr"
    );
  }
}//end if
