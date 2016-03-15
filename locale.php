#!/usr/bin/env php
<?php
/**
 * Project:  Locale
 * File:     locale.php
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

$shortopts = 'PCRDGTVnvsc:r:l:d:m:';
$longopts = [
  'parse', 'script-tag', 'region-tag', 'default-language', 'region-name',
  'script-name', 'variant-name', 'name', 'variant', 'system', 'script:',
  'region:', 'locale:', 'display:', 'match:'
];

if(!($opts = getopt($shortopts, $longopts))
   || (!isset($opts['s']) && empty($opts['l']))
)
{
  die(
    <<<EOT

locale.php <[-l code] [-s]> [-c code] [-d code] [-r code] [-m code] -[PCRDGTVnv]


All language codes used are from ISO-639. Country codes are from ISO-3166.
Script codes are from ISO-15924.

-l, --locale <language code>    ISO code to used as input.
-s, --system                    Get (PHP) system's default locale.

The following option uses Locale::getDisplayLanguage():
-d, --display <language code>   ISO code to used while display.

The following options use Locale::composeLocale():
-c, --script <script code>      Specifies the input script.
-r, --region <country code>     Specifies the input region.

The following option uses Locale::filterMatches():
-m, --match <language tag>      Check if the language tag specified by -l and/or

The following option uses Locale::getAllVariants():
-v, --variant                   Shows the variants for the input locale.

The following option uses Locale::getDisplayName():
-n, --name                      Shows an appropriately localized display name
                                for the input locale.

The following option uses Locale::getDisplayRegion():
-G, --region-name               Shows an appropriately localized display name
                                for region in the input locale.

The following option uses Locale::getDisplayScript():
-T, --script-name               Shows an appropriately localized display name
                                for script in the input locale.

The following option uses Locale::getDisplayVariant():
-V, --variant-name              Shows an appropriately localized display name
                                for variant in the input locale.

The following option uses Locale::getPrimaryLanguage():
-D, --default-language          Shows the code of primary language for the
                                input locale.

The following option uses Locale::getRegion():
-R, --region-tag               Shows the region code for the input locale.

The following option uses Locale::getScript():
-C, --script-tag               Shows the script code for the input locale.

The following option uses Locale::parseLocale():
-P, --parse                     Shows the input locale ID subtag elements.


Example:

./locale.php -l zh-hans -v -d zh-hant -GTVCRD


EOT
  );
}//end if

require_once 'func.php';

$opts = matchArguements($opts, $shortopts, $longopts);

$subtags = [];

/*
 *  opt --system
 */
if(isset($opts['s']))
{
  // display in the given language if --display is specified
  if(!empty($opts['d']))
  {
    die(Locale::getDisplayLanguage(Locale::getDefault(), $opts['d']) . PHP_EOL);
  }

  // otherwise display only the code
  die(Locale::getDefault() . PHP_EOL);
}

/*
 *  opt --script
 */
if(!empty($opts['c']))
{
  $subtags['script'] = $opts['c'];
}

/*
 *  opt --region
 */
if(!empty($opts['r']))
{
  $subtags['region'] = $opts['r'];
}

/*
 *  opt --language
 */
if(!empty($subtags) && !empty($opts['l']))
{
  $subtags['language'] = $opts['l'];
}

if(!empty($subtags))
{
  $locale = Locale::composeLocale($subtags);
}
else
{
  $locale = $opts['l'];
}

echo "input locale:\t\t\t$locale" . PHP_EOL;

/*
 *  opt --name
 */
if(isset($opts['n']))
{
  echo "Locale::getDisplayName():\t";

  if(empty($opts['d']))
  {
    echo Locale::getDisplayName($locale);
  }
  else
  {
    echo Locale::getDisplayName($locale, $opts['d']);
  }

  echo PHP_EOL;
}

/*
 *  opt --variant
 */
if(isset($opts['v']))
{
  $variants = Locale::getAllVariants($locale);

  echo "Locale::getAllVariants():\thas ";

  $count = count($variants);
  switch($count)
  {
    case 0:
      echo 'no variant';
      break;

    case 1:
      echo '1 variant';
      break;

    default:
      echo "$count variants";
  }

  echo PHP_EOL;

  foreach($variants as $variant)
  {
    echo " >  $variant" . PHP_EOL;
  }
}//end if

/*
 *  opt --region-name
 */
if(isset($opts['G']))
{
  echo "Locale::getDisplayRegion():\t";

  if(empty($opts['d']))
  {
    echo Locale::getDisplayRegion($locale);
  }
  else
  {
    echo Locale::getDisplayRegion($locale, $opts['d']);
  }

  echo PHP_EOL;
}

/*
 *  opt --script-name
 */
if(isset($opts['C']))
{
  echo "Locale::getDisplayScript():\t";

  if(empty($opts['d']))
  {
    echo Locale::getDisplayScript($locale);
  }
  else
  {
    echo Locale::getDisplayScript($locale, $opts['d']);
  }

  echo PHP_EOL;
}

/*
 *  opt --variant-name
 */
if(isset($opts['V']))
{
  echo "Locale::getDisplayVariant():\t";

  if(empty($opts['d']))
  {
    echo Locale::getDisplayVariant($locale);
  }
  else
  {
    echo Locale::getDisplayVariant($locale, $opts['d']);
  }

  echo PHP_EOL;
}

/*
 *  opt --default-language
 */
if(isset($opts['D']))
{
  echo "Locale::getPrimaryLanguage():\t" . Locale::getPrimaryLanguage($locale) .
       PHP_EOL;
}

/*
 *  opt --region-tag
 */
if(isset($opts['R']))
{
  echo "Locale::getRegion():\t\t" . Locale::getRegion($locale) . PHP_EOL;
}

/*
 *  opt --script-tag
 */
if(isset($opts['C']))
{
  echo "Locale::getScript():\t\t" . Locale::getScript($locale) . PHP_EOL;
}

/*
 *  opt --parse
 */
if(isset($opts['P']))
{
  echo 'Locale::parseLocale():' . PHP_EOL;

  $parsed = Locale::parseLocale($locale);
  foreach($parsed as $key => $value)
  {
    echo " >  $key\t=> $value" . PHP_EOL;
  }
}

/*
 *  opt --match
 */
if(!empty($opts['m']))
{
  echo "Locale::filterMatches():\t$locale";
  echo Locale::filterMatches($locale, $opts['m']) ? ' matches '
    : ' doesn\'t match ';
  die($opts['m'] . PHP_EOL);
}

echo "Locale::getDisplayLanguage():\t";
if(empty($opts['d']))
{
  echo Locale::getDisplayLanguage($locale);
}
else
{
  echo Locale::getDisplayLanguage($locale, $opts['d']);
}

die(PHP_EOL);
