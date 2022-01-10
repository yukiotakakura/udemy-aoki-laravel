<?php

namespace App\Constants;

class Common
{
  /** 在庫追加ラジオボタン */
  const PRODUCT_ADD = '1';
  /** 在庫削除ラジオボタン */
  const PRODUCT_REDUCE = '2';

  const PRODUCT_LIST = [
    'add' => self::PRODUCT_ADD,
    'reduce' => self::PRODUCT_REDUCE
  ];
  
  /** おすすめ順 */
  const ORDER_RECOMMEND = '0';
  /** 高い順 */
  const ORDER_HIGHER = '1';
  /** 安い順 */
  const ORDER_LOWER = '2';
  /** 古い順 */
  const ORDER_LATER = '3';
  /** 新着順 */
  const ORDER_OLDER = '4';

  const SORT_ORDER = [
    'recommend' => self::ORDER_RECOMMEND,
    'higherPrice' => self::ORDER_HIGHER,
    'lowerPrice' => self::ORDER_LOWER,
    'later' => self::ORDER_LATER,
    'older' => self::ORDER_OLDER
  ];

}