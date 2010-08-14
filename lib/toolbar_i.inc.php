<?php
/**
 * rep2 - ツールバー用ユーティリティ（iPhone）
 */

// {{{ _toolbar_i_button()

/**
 * ツールバーボタン (リンク)
 *
 * @param string $icon
 * @param string $label
 * @param string $uri
 * @param string $attrs
 * @return string
 */
function _toolbar_i_button($icon, $label, $uri, $attrs = '')
{
    static $hover = null;

    if ($hover === null) {
        $hover = implode(' ', array(
            '',
            'ontouchstart="iutil.toggleClass(this, \'hover\', true);"',
            'ontouchend="iutil.toggleClass(this, \'hover\', false);"',
            'ontouchcancel="iutil.toggleClass(this, \'hover\', false);"',
        ));
    }

    return <<<EOS
<a href="{$uri}"{$attrs}{$hover}><img src="{$icon}" width="48" height="32" alt=""><br>{$label}</a>
EOS;
}

// }}}
// {{{ toolbar_i_standard_button()

/**
 * 標準のツールバーボタン (リンク)
 *
 * @param string $icon
 * @param string $label
 * @param string $uri
 * @return string
 */
function toolbar_i_standard_button($icon, $label, $uri)
{
    return _toolbar_i_button($icon, $label, $uri);
}

// }}}
// {{{ toolbar_i_badged_button()

/**
 * バッジ付きのツールバーボタン (リンク)
 *
 * @param string $icon
 * @param string $label
 * @param string $uri
 * @param string $badge
 * @return string
 */
function toolbar_i_badged_button($icon, $label, $uri, $badge)
{
    $label .= sprintf('<span class="badge l%d">%s</span>', min(strlen($badge), 4), $badge);
    return _toolbar_i_button($icon, $label, $uri);
}

// }}}
// {{{ toolbar_i_opentab_button()

/**
 * リンクを新しいタブで開くツールバーボタン
 *
 * @param string $icon
 * @param string $label
 * @param string $uri
 * @return string
 */
function toolbar_i_opentab_button($icon, $label, $uri)
{
    return _toolbar_i_button($icon, $label, $uri, ' target="_blank"');
}

// }}}
// {{{ toolbar_i_disabled_button()

/**
 * 無効なツールバーボタン
 *
 * @param string $icon
 * @param string $label
 * @param string $uri
 * @return string
 */
function toolbar_i_disabled_button($icon, $label)
{
    return <<<EOS
<span class="unavailable"><img src="{$icon}" width="48" height="32" alt=""><br>{$label}</span>
EOS;
}

// }}}
// {{{ toolbar_i_showhide_button()

/**
 * ターゲット要素の表示・非表示をトグルするツールバーボタン
 *
 * @param string $icon
 * @param string $label
 * @param string $id
 * @return string
 */
function toolbar_i_showhide_button($icon, $label, $id)
{
    $attrs = ' onclick="return iutil.toolbarShowHide(this, event);"';
    return _toolbar_i_button($icon, $label, "#{$id}", $attrs);
}

// }}}
// {{{ toolbar_i_favita_button()

/**
 * お気に板の登録・解除をトグルするツールバーボタン
 *
 * @param string $icon
 * @param string $label (fallback)
 * @param object $info @see lib/get_info.inc.php: get_board_info()
 * @param int $setnum
 * @return string
 */
function toolbar_i_favita_button($icon, $label, $info, $setnum = 0)
{
    if (!array_key_exists($setnum, $info->favs)) {
        return toolbar_i_disabled_button($icon, $label);
    }

    $fav = $info->favs[$setnum];
    $attrs = ' onclick="return iutil.toolbarSetFavIta(this, event);"';
    if (!$fav['set']) {
        $attrs .= ' class="inactive"';
    }
    $uri = 'httpcmd.php?' . http_build_query(array(
        'cmd'       => 'setfavita',
        'host'      => $info->host,
        'bbs'       => $info->bbs,
        'itaj_en'   => UrlSafeBase64::encode($info->itaj),
        'setnum'    => $setnum,
        'setfavita' => -1,
    ), '', '&amp;');

    return _toolbar_i_button($icon, $fav['title'], $uri, $attrs);
}

// }}}
// {{{ toolbar_i_fav_button()

/**
 * お気にスレの登録・解除をトグルするツールバーボタン
 *
 * @param string $icon
 * @param string $label (fallback)
 * @param object $info @see lib/get_info.inc.php: get_thread_info()
 * @param int $setnum
 * @return string
 */
function toolbar_i_fav_button($icon, $label, $info, $setnum = 0)
{
    if (!array_key_exists($setnum, $info->favs)) {
        return toolbar_i_disabled_button($icon, $label);
    }

    $fav = $info->favs[$setnum];
    $attrs = ' onclick="return iutil.toolbarSetFav(this, event);"';
    if (!$fav['set']) {
        $attrs .= ' class="inactive"';
    }
    $uri = 'httpcmd.php?' . http_build_query(array(
        'cmd'       => 'setfav',
        'host'      => $info->host,
        'bbs'       => $info->bbs,
        'key'       => $info->key,
        'ttitle_en' => UrlSafeBase64::encode($info->ttitle),
        'setnum'    => $setnum,
        'setfav'    => -1,
    ), '', '&amp;');

    return _toolbar_i_button($icon, $fav['title'], $uri, $attrs);
}

// }}}
// {{{ toolbar_i_action_board_button()

/**
 * 外部アプリ等で板を開くボタン
 *
 * @param string $icon
 * @param string $label (fallback)
 * @param ThreadList $aThreadList
 * @return string
 */
function toolbar_i_action_board_button($icon, $label, ThreadList $aThreadList)
{
    global $_conf;

    $type = _toolbar_i_client_type();
    $pattern = $_conf["expack.tba.{$type}.board_uri"];
    if (!$pattern) {
        return toolbar_i_disabled_button($icon, $label);
    }

    $host = $aThreadList->host;
    $bbs = $aThreadList->bbs;
    $url = "http://{$host}/{$bbs}/";
    $uri = htmlspecialchars(strtr($pattern, array(
        '$time' => time(),
        '$host' => rawurlencode($host),
        '$bbs'  => rawurlencode($bbs),
        '$url'  => $url,
        '$eurl' => rawurlencode($url),
    )), ENT_QUOTES, 'Shift_JIS', false);

    $title = $_conf["expack.tba.{$type}.board_title"];
    if ($title !== '') {
        $label = htmlspecialchars($title, ENT_QUOTES, 'Shift_JIS', false);
    }

    return _toolbar_i_button($icon, $label, $uri);
}

// }}}
// {{{ toolbar_i_action_thread_button()

/**
 * 外部アプリ等でスレッドを開くボタン
 *
 * @param string $icon
 * @param string $label (fallback)
 * @param Thread $aThread
 * @return string
 */
function toolbar_i_action_thread_button($icon, $label, Thread $aThread)
{
    global $_conf;

    $type = _toolbar_i_client_type();
    $pattern = $_conf["expack.tba.{$type}.thread_uri"];
    if (!$pattern) {
        return toolbar_i_disabled_button($icon, $label);
    }

    $url = $aThread->getMotoThread(true, '');
    $uri = htmlspecialchars(strtr($pattern, array(
        '$time' => time(),
        '$host' => rawurlencode($aThread->host),
        '$bbs'  => rawurlencode($aThread->bbs),
        '$key'  => rawurlencode($aThread->key),
        '$ls'   => rawurlencode($aThread->ls),
        '$url'  => $url,
        '$eurl' => rawurlencode($url),
        '$path' => preg_replace('!^https?://!', '', $url),
    )), ENT_QUOTES, 'Shift_JIS', false);

    $title = $_conf["expack.tba.{$type}.thread_title"];
    if ($title !== '') {
        $label = htmlspecialchars($title, ENT_QUOTES, 'Shift_JIS', false);
    }

    return _toolbar_i_button($icon, $label, $uri);
}

// }}}
// {{{ _toolbar_i_client_type()

/**
 * クライアントの種類を返す
 *
 * @param void
 * @return string
 */
function _toolbar_i_client_type()
{
    global $_conf;

    switch ($_conf['client_type']) {
        case 'i':
            $type = UA::isAndroidWebKit() ? 'android' : 'iphone';
            break;
        case 'i':
            $type = 'mobile';
            break;
        case 'pc':
        default:
            $type = 'other';
    }

    return $type;
}

// }}}

/*
 * Local Variables:
 * mode: php
 * coding: cp932
 * tab-width: 4
 * c-basic-offset: 4
 * indent-tabs-mode: nil
 * End:
 */
// vim: set syn=php fenc=cp932 ai et ts=4 sw=4 sts=4 fdm=marker:
