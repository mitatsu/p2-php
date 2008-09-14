<?php
/**
 * rep2expack - 書き込みフォームへの追加要素 for PC
 */

// 書き込みフォームをモナーフォントにするスイッチを追加
if ($_conf['expack.editor.with_aMona']) {
    $htm['options'] .=  <<<EOM
    <select id="MONAFONT" name="MONAFONT" onchange="activeMonaForm(this.options[this.selectedIndex].value);">
        <option value="">(´∀｀)</option>
        <option value="normal">Normal</option>
        <option value="16px">Mona-16</option>
        <option value="14px">Mona-14</option>
        <option value="12px">Mona-12</option>
    </select>\n
EOM;
}

// 定型文メニューを追加
if ($_conf['expack.editor.constant']) {
    // 定型文の初期化と読み込み
    $CONSTANT = array();
    @include './conf/conf_constant.php';
    // フォームの生成
    $js['dp_cnstmsg'] = '';
    if ($_conf['expack.editor.dpreview']) {
        $js['dp_cnstmsg'] = "DPSetMsg(document.getElementById('MESSAGE').value);";
    }
    $htm['options'] .=<<<EOS
    <select id="CONSTANT" name="CONSTANT" onchange="inputConstant(this);{$js['dp_cnstmsg']}">
        <option value="">φ</option>\n
EOS;
    foreach ($CONSTANT as $constant_key => $constant_value) {
        $htm['options'] .= "\t\t<option value=\"{$constant_value}\">{$constant_key}</option>\n";
    }
    $htm['options'] .= "\t</select>\n";
}

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
