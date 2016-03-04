<?php
/**
 * Project:  Locale
 * File:     func.php
 * Created:  2016-03-04
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

use Alcohol\ISO3166\ISO3166;

/**
 * Matches and merges all long options with short options.
 *
 * The long and short option tags must has the matching numeric index.
 *
 * @param array  $opts      Command line options got
 * @param string $shortTags List of short option tags
 * @param array  $longTags  List of long options tags
 *
 * @return array
 */
function matchArguements($opts, $shortTags, $longTags)
{
  foreach(str_split(str_replace(':', '', $shortTags)) as $idx => $opt)
  {
    $longopt = str_replace(':', '', $longTags[$idx]);
    if(isset($opts[$longopt]))
    {
      if(!isset($opts[$opt]))
      {
        $opts[$opt] = $opts[$longopt];
      }
      else
      {
        $opts[$longopt] = is_array($opts[$longopt])
          ? : [$opts[$longopt]];
        $opts[$opt] = is_array($opts[$opt]) ? : [$opts[$opt]];
        $opts[$opt] = array_merge($opts[$opt], $opts[$longopt]);
      }
    }
  }//end foreach

  return $opts;
}//end matchArguements()

//////////////////////////////////////////////////////////////////////////
//
// Functions used in formatter.php
//
//////////////////////////////////////////////////////////////////////////

/**
 * Prints the value or error message.
 *
 * @param mixed  $value
 * @param string $tag
 */
function printValue($value, $tag)
{
  global $formatter, $displayLocale, $style;

  if(empty($tag))
  {
    $tag = ' >  ';
  }

  if(false === $value)
  {
    if(!$formatter)
    {
      $formatter = new NumberFormatter($displayLocale, $style);
    }

    printf("% -45s%s%s", "$tag:", 'error occurred', PHP_EOL);
    printf(
      "\t% -45s%d%s", '->getErrorCode:', $formatter->getErrorCode(), PHP_EOL
    );
    printf(
      "\t% -45s%s%s", '->getErrorMessage:', $formatter->getErrorMessage(),
      PHP_EOL
    );
  }

  printf("% -45s%s%s", "$tag:", $value, PHP_EOL);
}//end printValue()

/**
 * Gets the format style from command line options
 *
 * @param array $opts Command line options got
 *
 * @return int
 */
function getStyle($opts)
{
  $style = empty($opts['s']) ? '' : strtolower(substr($opts['s'], 0, 3));
  switch($style)
  {
    case 'pat':
      $style = NumberFormatter::PATTERN_DECIMAL;
      echo '::PATTERN_DECIMAL style requested' . PHP_EOL;
      break;

    case 'dec':
      $style = NumberFormatter::DECIMAL;
      echo '::DECIMAL style requested' . PHP_EOL;
      break;

    case 'cur':
      $style = NumberFormatter::CURRENCY;
      echo '::CURRENCY style requested' . PHP_EOL;
      break;

    case 'per':
      $style = NumberFormatter::PERCENT;
      echo '::PERCENT style requested' . PHP_EOL;
      break;

    case 'sci':
      $style = NumberFormatter::SCIENTIFIC;
      echo '::SCIENTIFIC style requested' . PHP_EOL;
      break;

    case 'spe':
      $style = NumberFormatter::SPELLOUT;
      echo '::SPELLOUT style requested' . PHP_EOL;
      break;

    case 'ord':
      $style = NumberFormatter::ORDINAL;
      echo '::ORDINAL style requested' . PHP_EOL;
      break;

    case 'dur':
      $style = NumberFormatter::DURATION;
      echo '::DURATION style requested' . PHP_EOL;
      break;

    case 'rul':
      $style = NumberFormatter::PATTERN_RULEBASED;
      echo '::PATTERN_RULEBASED style requested' . PHP_EOL;
      break;

    default:
      $style = NumberFormatter::DEFAULT_STYLE;
      echo '::DEFAULT_STYLE style requested' . PHP_EOL;
  }//end switch

  return $style;
}//end getStyle()

/**
 * Get value type from command line options
 *
 * @param array $opts Command line options got
 *
 * @return int
 */
function getValueType($opts)
{
  $type = empty($opts['t']) ? '' : strtolower($opts['t']);
  switch($type)
  {
    case '32':
      $type = NumberFormatter::TYPE_INT32;
      echo '::TYPE_INT32 data type requested' . PHP_EOL;
      break;

    case '64':
      $type = NumberFormatter::TYPE_INT64;
      echo '::TYPE_INT64 data type requested' . PHP_EOL;
      break;

    case 'f':
      $type = NumberFormatter::TYPE_DOUBLE;
      echo '::TYPE_DOUBLE data type requested' . PHP_EOL;
      break;

    case 'c':
      $type = NumberFormatter::TYPE_CURRENCY;
      echo '::TYPE_CURRENCY data type requested' . PHP_EOL;
      break;

    default:
      $type = NumberFormatter::TYPE_DEFAULT;
      echo '::TYPE_DEFAULT data type requested' . PHP_EOL;
  }//end switch

  return $type;
}//end getValueType()

//////////////////////////////////////////////////////////////////////////
//
// Functions used in iso3166.php
//
//////////////////////////////////////////////////////////////////////////

/**
 * Get a given code
 *
 * @param array                   $opts
 * @param Alcohol\ISO3166\ISO3166 $codes
 */
function getCode($opts, $codes)
{
  if(isset($opts['2']))
  {
    $method = 'getByAlpha2';
  }
  elseif(isset($opts['3']))
  {
    $method = 'getByAlpha3';
  }
  elseif(isset($opts['n']))
  {
    $method = 'getByNumeric';
  }
  else
  {
    $method = 'getByCode';
  }

  echo "$method():" . PHP_EOL;
  printArray($codes->$method($opts['c']));
}//end getCode()

/**
 * Get all codes
 *
 * @param array                   $opts
 * @param Alcohol\ISO3166\ISO3166 $codes
 */
function getList($opts, $codes)
{
  if(isset($opts['2']))
  {
    $const = 'ISO3166::KEY_ALPHA2';
    $key = ISO3166::KEY_ALPHA2;
  }
  elseif(isset($opts['3']))
  {
    $const = 'ISO3166::KEY_ALPHA3';
    $key = ISO3166::KEY_ALPHA3;
  }
  elseif(isset($opts['n']))
  {
    $const = 'ISO3166::KEY_NUMERIC';
    $key = ISO3166::KEY_NUMERIC;
  }
  else
  {
    echo 'Using default iterator:' . PHP_EOL;
    printArray($codes);

    return;
  }//end if

  echo "Using listBy($const):" . PHP_EOL;
  printArray($codes->listBy($key));
}//end getList()

/**
 * Format array to string.
 *
 * @param array|\IteratorAggregate|\Generator $array
 * @param string                              $indent
 * @param bool                                $withKey
 *
 * @return void
 */
function printArray($array, $indent = ' >  ', $withKey = true)
{
  if($withKey)
  {
    foreach($array as $key => $value)
    {
      if(is_array($value))
      {
        echo "$indent$key    \t=> {" . PHP_EOL;
        printArray($value, "$indent\t");
        echo "$indent}" . PHP_EOL;
      }
      else
      {
        echo "$indent$key    \t=> $value" . PHP_EOL;
      }
    }
  }
}//end printArray()
