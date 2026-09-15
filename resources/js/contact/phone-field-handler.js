/**
 * 電話番号フィールドの結合処理と復元処理
 */

export function initPhoneField() {
    const tel1 = document.getElementById('tel1');
    const tel2 = document.getElementById('tel2');
    const tel3 = document.getElementById('tel3');
    const telHidden = document.getElementById('tel');
    const form = document.querySelector('form');

    if (!tel1 || !tel2 || !tel3 || !telHidden || !form) {
        return;
    }

    // 【追加】確認画面から戻ってきた時など、すでに結合されたデータ(telHidden)がある場合に3つのマスに復元する処理
    if (telHidden.value && (!tel1.value && !tel2.value && !tel3.value)) {
        // 例: 08012345678 などの数字を、一般的な市外局番や携帯の桁数に合わせて自動分割します
        const fullTel = telHidden.value.replace(/-/g, ''); // ハイフンを一旦除去
        
        if (fullTel.length >= 10) {
            // 携帯番号や主要な固定電話の桁数(3桁-4桁-4桁 または 3桁-3桁-4桁など)に対応して切り分けます
            tel1.value = fullTel.substring(0, 3);
            if (fullTel.length === 11) {
                tel2.value = fullTel.substring(3, 7);
                tel3.value = fullTel.substring(7, 11);
            } else {
                tel2.value = fullTel.substring(3, 6);
                tel3.value = fullTel.substring(6, 10);
            }
        }
    }

    function updateTel() {
        const telValue = tel1.value + tel2.value + tel3.value;
        telHidden.value = telValue.replace(/^-|-$/g, '').replace(/--+/g, '-');
    }

    tel1.addEventListener('input', updateTel);
    tel2.addEventListener('input', updateTel);
    tel3.addEventListener('input', updateTel);
    form.addEventListener('submit', updateTel);
}