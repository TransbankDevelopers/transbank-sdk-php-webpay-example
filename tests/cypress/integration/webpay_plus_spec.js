describe('Using Webpay Plus', function() {
  it('is possible to pay with credit card', function() {
    cy.visit('/')

    cy.contains('Webpay Plus Normal').should('be.visible').click()

    cy.contains('Continuar »').should('be.visible').click()

    cy.contains('Selecciona tu medio de pago')

    cy.contains('Tarjeta de crédito').should('be.visible').click()
    
    // Formulario Tarjeta de Crédito
    cy.get('form button').should('have.class', 'disabled')
    cy.contains('Número de tarjeta')
    cy.get('input[name=cardNumber]').type('4051 8856 0044 6623')

    cy.contains('Fecha de vencimiento')
    cy.get('select[name="month"]').select('12')
    cy.get('select[name="year"]').select(`${new Date().getFullYear()}`)

    cy.contains('Código de seguridad')
    cy.get('input[name="cvv"]').type('123')
    cy.get('form button.button.new-marg.next-padd').should('not.have.class', 'disabled').should('be.visible').click()

    cy.contains('Cantidad de cuotas')
    cy.contains('Sin Cuotas')
    cy.get('form button.button.new-marg.next-padd').should('not.have.class', 'disabled').should('be.visible').click()

    cy.wait(4000)

    // Formulario Autenticación banco
    cy.get('#control frame[name=transicion]').then(($frame) => {
      cy.wrap($frame.contents().find('form[name=frm]')).find('#rutClient').type('11.111.111-1')
      cy.wrap($frame.contents().find('form[name=frm]')).find('#passwordClient').type('123')
      cy.wrap($frame.contents().find('form[name=frm]')).find('input[type=submit]').should('be.visible').click()
    })

    cy.get('#control frame[name=transicion]').then(($frame) => {
      cy.wrap($frame.contents().find('body')).find('input[type=submit]').should('be.visible').click()
    })

    // Resultado

    cy.contains('Continuar »').should('be.visible').click()
    cy.contains('Ir a detalle de la compra').scrollIntoView().should('be.visible').click()
    cy.contains('Transacion Finalizada')
    cy.get('input[value="Anular Transacción »"]').should('be.visible').click()
  })
})