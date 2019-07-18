describe('Using Webpay Oneclick', function() {
  it('is possible to pay with credit card', function() {
    cy.visit('/')

    cy.contains('Webpay OneClick Normal').should('be.visible').click()

    cy.contains('Continuar »').should('be.visible').click()

    // Formulario Tarjeta de Crédito
    cy.get('form button').should('have.class', 'disabled').should('be.visible')
    cy.contains('Número de tarjeta').should('be.visible')
    cy.get('input[name=cardNumber]').should('be.visible').type('4051 8856 0044 6623')

    cy.contains('Fecha de vencimiento')
    cy.get('select[name="month"]').should('be.visible').select('12')
    cy.get('select[name="year"]').should('be.visible').select(`${new Date().getFullYear()}`)

    cy.contains('Código de seguridad')
    cy.get('input[name="cvv"]').should('be.visible').type('123')
    cy.get('form button.button.new-marg.next-padd').should('not.have.class', 'disabled').should('be.visible').click()

    cy.wait(6000)

    // Formulario Autenticación banco
    cy.get('#control frame[name=transicion]').then(($frame) => {
      cy.wrap($frame.contents().find('form[name=frm]')).find('#rutClient').should('be.visible').type('11.111.111-1')
      cy.wrap($frame.contents().find('form[name=frm]')).find('#passwordClient').should('be.visible').type('123')
      cy.wrap($frame.contents().find('form[name=frm]')).find('input[type=submit]').should('be.visible').click()
    })

    cy.wait(1000)
    
    cy.get('#control frame[name=transicion]').then(($frame) => {
      cy.wrap($frame.contents().find('form[name=frm]')).find('input[type=submit]').click()
    })

    cy.wait(6000)

    // Authorizar
    cy.contains('Transacción ACEPTADA por webpay')
    cy.contains('Continuar »').should('be.visible').click()

    // Reversa
    cy.contains('Transacción ACEPTADA por webpay')
    cy.contains('Continuar »').should('be.visible').click()

    // Finalizar inscripción
    cy.contains('Transacción ACEPTADA por webpay')
    cy.contains('Continuar »').should('be.visible').click()

    // Remove user
    cy.contains('Transacción ACEPTADA por webpay')
  })
})