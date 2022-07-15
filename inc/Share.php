<?php
class Share extends Entity {
  public static $dbFields = ['reference'];
  public static $dbTable = 'share';

  function formEdit () {
    return [
      'reference' => [
        'name' => 'Reference',
        'type' => 'text',
      ],
    ];
  }
}
