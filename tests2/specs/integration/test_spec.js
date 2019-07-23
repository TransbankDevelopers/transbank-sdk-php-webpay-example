const assert = require('assert');

describe('Using Webpay Plus', () => {
    it('should let you pay with credit card', () => {
        browser.url('http://web');
        console.log(browser.getText('html'));
        $('a=Webpay Plus Normal').click();
        console.log('Despues de clickear el link');
        console.log(browser.getText('html'));
        $('input[value="Continuar »"]').click();
        // Change this, it's awful, just leave it to test travis
        $('h1=Selecciona tu medio de pago').waitForExist();
        $('h3=Tarjeta de crédito').click();
        $('input[name="cardNumber"]').setValue("4051 8856 0044 6623");
        $('input[name="cvv"]').setValue("123");
        $('button.button.new-marg.next-padd').click();
        $('button.button.new-marg.next-padd').click();
        browser.pause(6000);
        $('frameset>frame[name="transicion"]').waitForExist();
        browser.frame($('frameset>frame[name="transicion"]').value);
        $('input#rutClient').waitForExist();
        $('input#rutClient').setValue('11.111.111-1');
        $('input#passwordClient').setValue('123');
        $('input[type=submit]').click();
        browser.pause(3000);
        $('input[type=submit]').click();
        $('samp=Pago ACEPTADO por webpay (se deben guardatos para mostrar voucher)').waitForExist();
        $('input[value="Continuar »"]').click();
        $('a=Ir a detalle de la compra').click();
        $('samp=Transacion finalizada')
    });
});
