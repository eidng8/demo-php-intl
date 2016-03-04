#!/usr/bin/env php
<?php
/**
 * Project:  Locale
 * File:     iso3166.php
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

use Alcohol\ISO3166\ISO3166;

require __DIR__ . '/vendor/autoload.php';

$opts = '23nlc:';
$longopts = [
  'alpha2', 'alpha3', 'numeric', 'list', 'code'
];

if(!($opts = getopt($opts, $longopts))
   || (!isset($opts['l']) && empty($opts['c']))
)
{
  die(
    <<<EOT

iso3166.php <[-l] [-c code]> -[23n]


Country codes are from ISO-3166.
Credit goes to http://alcohol.github.io/iso3166/

-l, --list                      Lists all available ISO codes.
-c, --code                      Shows information regarding the specified code.


-2, --alpha2
-3, --alpha3
-n, --numeric
                                Used in conjunction with -l and -c, to specify
                                the input format.

EOT
  );
}//end if

require_once 'func.php';

$opts = matchArguements($opts, $shortopts, $longopts);

$codes = new ISO3166();

try
{
  if(!empty($opts['c']))
  {
    getCode($opts, $codes);
  }
  elseif(isset($opts['l']))
  {
    getList($opts, $codes);
  }
}//end try
catch(\Exception $ex)
{
  echo $ex->getMessage();
}//end catch
