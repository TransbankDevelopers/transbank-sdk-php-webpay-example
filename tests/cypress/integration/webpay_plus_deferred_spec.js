describe('Using Webpay Plus Deferred', function() {
  it('is possible to pay with credit card', function() {
    cy.visit('/')

    cy.contains('Webpay Plus Captura Diferida').click()

    cy.contains('Continuar').click()
    
    // Formulario Tarjeta de Crédito
    cy.get('form button').should('have.class', 'disabled')
    cy.contains('Número de tarjeta')
    cy.get('input[name=cardNumber]').type('4051 8856 0044 6623')

    cy.contains('Fecha de vencimiento')
    cy.get('select[name="month"]').select('12')
    cy.get('select[name="year"]').select(`${new Date().getFullYear()}`)

    cy.contains('Código de seguridad')
    cy.get('input[name="cvv"]').type('123')
    cy.get('form button.button.new-marg.next-padd').should('not.have.class', 'disabled').click()

    cy.contains('Cantidad de cuotas')
    cy.contains('Sin Cuotas')
    cy.get('form button.button.new-marg.next-padd').should('not.have.class', 'disabled').click()

    cy.wait(6000)

    // Formulario Autenticación banco
    cy.get('#control frame[name=transicion]').then(($frame) => {
      cy.wrap($frame.contents().find('form[name=frm]')).find('#rutClient').type('11.111.111-1')
      cy.wrap($frame.contents().find('form[name=frm]')).find('#passwordClient').type('123')
      cy.wrap($frame.contents().find('form[name=frm]')).find('input[type=submit]').click()
    })

    cy.wait(1000)

    cy.get('#control frame[name=transicion]').then(($frame) => {
      cy.wrap($frame.contents().find('form[name=frm]')).find('input[type=submit]').click()
    })

    // For some reason the form that redirects back to the example project has an attribute "target"
    // that breaks cypress, if it ever gets removed or changed, modify the following lines
    cy.get('form#paso').should('have.attr', 'target', '_parent')
      .invoke('attr', 'target', null)

    // Resultado
    cy.contains('Continuar').click()
    cy.contains('Ir a detalle de la compra').scrollIntoView().should('be.visible').click()
    cy.get('input[type=submit]').click()
    cy.contains('Transación Finalizada')
  })
})