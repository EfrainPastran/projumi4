<?php
namespace Tests\Selenium;

use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverBy;

class TestVentasPresencial extends BaseTest
{
    private function takeStepScreenshot($stepNumber, $description)
    {
        $dir = __DIR__ . "/screenshots";
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $file = "$dir/ventas_step{$stepNumber}_" . time() . ".png";
        $this->driver->takeScreenshot($file);

        return "Paso $stepNumber: $description ✅ Screenshot: $file\n";
    }

    private function loginUsuario($cedula, $password)
    {
        $notes = "";

        // Ir a la página principal
        $this->driver->get($this->baseUrl.'/home/principal');
        $notes .= $this->takeStepScreenshot(1, "Página principal cargada");

        // Abrir modal de login
        $this->openLoginModal();
        $notes .= $this->takeStepScreenshot(2, "Modal de login abierto");

        // Ingresar credenciales
        $cedulaField = $this->driver->findElement(WebDriverBy::id('ced'));
        $passwordField = $this->driver->findElement(WebDriverBy::id('pass'));
        $loginButton = $this->driver->findElement(WebDriverBy::cssSelector('input[name="iniciar"]'));

        $cedulaField->sendKeys($cedula);
        $passwordField->sendKeys($password);
        $notes .= $this->takeStepScreenshot(3, "Credenciales ingresadas: $cedula / $password");

        // Click en iniciar sesión
        $loginButton->click();
        $notes .= $this->takeStepScreenshot(4, "Botón de login clickeado");

        // Esperar a que el login se complete
        sleep(2); // o usar WebDriverWait para un elemento visible post-login

        return $notes;
    }

    private function abrirVentasPresencial()
    {
        $wait = new WebDriverWait($this->driver, 10);

        // Esperar a que el botón "Registrar Ventas" esté presente
        $wait->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::cssSelector('button[data-bs-target="#registrarVentaModal"]')
            )
        );
    }

public function testRegistroExitoso()
{
    $notes = "";
    $wait = new WebDriverWait($this->driver, 10);

    // 1️⃣ Login
    $notes .= $this->loginUsuario('27759045', '12345678');

    // 2️⃣ Ir a Ventas
    $this->driver->get($this->baseUrl . '/ventaspresencial/index');
    $notes .= $this->takeStepScreenshot(5, "Página Ventas Presenciales cargada");

    $this->abrirVentasPresencial();
    $notes .= $this->takeStepScreenshot(6, "Botón Registrar disponible");

    // 3️⃣ Abrir formulario
    $registrarBtn = $this->driver->findElement(
        WebDriverBy::cssSelector('button[data-bs-target="#registrarVentaModal"]')
    );
    $registrarBtn->click();
    $notes .= $this->takeStepScreenshot(7, "Formulario de registro abierto");

    // 4️⃣ Cédula → Autocompletado
    $cedulaField = $this->driver->findElement(WebDriverBy::id('cedula'));
    $cedulaField->sendKeys('7363406');

    // esperar autocompletar
    $wait->until(function ($driver) {
        return !empty(
            $driver->findElement(WebDriverBy::id('nombre'))->getAttribute('value')
        );
    });

    $notes .= $this->takeStepScreenshot(8, "Cliente autocompletado correctamente");

    // 5️⃣ Agregar producto (JS)
$btnAgregarProducto = $this->driver->findElement(
    WebDriverBy::cssSelector('#btnAgregarProducto')
);
$btnAgregarProducto->click();

    sleep(1);

    // Seleccionar producto ID 23
    $productoSelect = $this->driver->findElement(
        WebDriverBy::cssSelector('#tablaProductosVendidos select.producto-select')
    );

    $this->driver->executeScript("
        arguments[0].value = '23';
        arguments[0].dispatchEvent(new Event('change', { bubbles:true }));
    ", [$productoSelect]);

    // Cantidad
    $cantidadInput = $this->driver->findElement(
        WebDriverBy::cssSelector('#tablaProductosVendidos input.cantidad-input')
    );

    $this->driver->executeScript("
        arguments[0].value = '1';
        arguments[0].dispatchEvent(new Event('input', { bubbles:true }));
    ", [$cantidadInput]);

    sleep(1);

    $notes .= $this->takeStepScreenshot(9, "Producto agregado correctamente");

    // 6️⃣ Agregar método de pago
    $metodoSelect = $this->driver->findElement(
        WebDriverBy::cssSelector('#desglosePagoBody select.metodo-desglose')
    );

    $this->driver->executeScript("
        arguments[0].value = '1';
        arguments[0].dispatchEvent(new Event('change', { bubbles:true }));
    ", [$metodoSelect]);

    // Moneda
    $monedaSelect = $this->driver->findElement(
        WebDriverBy::cssSelector('#desglosePagoBody select.moneda-desglose')
    );

    $this->driver->executeScript("
        arguments[0].value = '2';
        arguments[0].dispatchEvent(new Event('change', { bubbles:true }));
    ", [$monedaSelect]);

    // Monto
    $montoInput = $this->driver->findElement(
        WebDriverBy::cssSelector('#desglosePagoBody input.monto-desglose')
    );

    $this->driver->executeScript("
        arguments[0].value = '1';
        arguments[0].dispatchEvent(new Event('input', { bubbles:true }));
    ", [$montoInput]);

    // 7️⃣ Esperar TOTAL calculado
    $wait->until(function ($driver) {
        $total = $driver->findElement(WebDriverBy::id('totalPagarPedido'))->getText();
        return !empty($total) && $total !== "0" && $total !== "0.00";
    });

    $notes .= $this->takeStepScreenshot(10, "Desglose de pago completado");

    // 8️⃣ Registrar la venta (Submit JS)
    $form = $this->driver->findElement(WebDriverBy::id('formRegistrarVenta'));
    $this->driver->executeScript("
        arguments[0].dispatchEvent(new Event('submit', { bubbles:true, cancelable:true }));
    ", [$form]);

    // 9️⃣ Esperar SweetAlert éxito
    $alert = $wait->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(
            WebDriverBy::cssSelector('.swal2-success')
        )
    );

    $notes .= $this->takeStepScreenshot(11, "SweetAlert detectado");

    // ️🔟 Validar
    if ($alert->isDisplayed()) {
        $notes .= "✅ Venta registrada correctamente\n";
        $this->reportTestResult('SGP-40', true, $notes);
    } else {
        $notes .= "❌ El SweetAlert no apareció\n";
        $this->reportTestResult('SGP-40', false, $notes);
    }
}



    public function testDatosInvalidos()
    {
        $notes = "";

        // 1️⃣ Hacer login
        $notes .= $this->loginUsuario('27759045', '12345678');

        // 2️⃣ Abrir la URL de Ventas Presenciales directamente
        $this->driver->get($this->baseUrl . '/ventaspresencial/index');
        $notes .= $this->takeStepScreenshot(5, "Página Ventas Presenciales cargada");

        // Esperar a que se muestre el botón Registrar Ventas
        $this->abrirVentasPresencial();
        $notes .= $this->takeStepScreenshot(6, "Formulario de registro disponible");

        // Click en "Registrar ventas"
        $registrarBtn = $this->driver->findElement(WebDriverBy::cssSelector('button[data-bs-target="#registrarVentaModal"]'));
        $registrarBtn->click();
        $notes .= $this->takeStepScreenshot(7, "Formulario abierto");

        // Llenar campos con datos inválidos
        $this->driver->findElement(WebDriverBy::id('cedula'))->sendKeys('');
        $this->driver->findElement(WebDriverBy::id('nombre'))->sendKeys('');
        $this->driver->findElement(WebDriverBy::id('apellido'))->sendKeys('');
        $this->driver->findElement(WebDriverBy::id('correo'))->sendKeys('correo_invalido');
        $notes .= $this->takeStepScreenshot(8, "Formulario completado con datos inválidos");

        // Click en Registrar Ventas
        $this->driver->findElement(WebDriverBy::cssSelector('#formRegistrarVenta button[type="submit"]'))->click();
        sleep(2);

        // Validar mensaje de error
        $alert = $this->driver->findElement(WebDriverBy::cssSelector('.swal2-error'));
        if ($alert->isDisplayed()) {
            $notes .= "✅ Mensaje de error detectado correctamente\n";
            $this->reportTestResult('SGP-42', true, $notes);
        } else {
            $notes .= "❌ No se detectó mensaje de error\n";
            $this->reportTestResult('SGP-42', false, $notes);
        }
    }
}
