const { Builder, By, until } = require('selenium-webdriver');
const { baseUrl, sessionCookie } = require('./config'); // Importa la config

(async function accesoConCookies() {
  let driver = await new Builder().forBrowser('chrome').build();
  try {
    // 1. Ir al dominio base
    await driver.get(baseUrl);

    // 2. Agregar cookie desde config
    await driver.manage().addCookie(sessionCookie);

    const inicio = new Date();

    // 3. Ir a la vista protegida
    await driver.get(`${baseUrl}/inicio/index`);

    // Esperar a que cargue la página
    await driver.wait(until.elementLocated(By.css('body')), 10000);

    const fin = new Date();
    const tiempo = (fin.getTime() - inicio.getTime()) / 1000;
    console.log(`Tiempo de carga: ${tiempo} segundos`);

  } catch (err) {
    console.error(' Error durante la prueba:', err);
  } finally {
    await driver.quit();
  }
})();
