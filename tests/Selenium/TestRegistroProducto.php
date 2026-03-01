<?php
namespace Tests\Selenium;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class TestRegistroProducto extends BaseTest
{
    private function takeStepScreenshot($stepNumber, $description)
    {
        $dir = __DIR__ . "/screenshots";
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $file = "$dir/step{$stepNumber}_" . time() . ".png";
        $this->driver->takeScreenshot($file);

        return "Paso $stepNumber: $description 📸 Screenshot: $file\n";
    }

    private function login()
    {
        $notes = "";

        // 1️⃣ Página principal
        $this->driver->get($this->baseUrl . '/home/principal');
        $notes .= $this->takeStepScreenshot(1, "Página principal cargada");

        // 2️⃣ Abrir login
        $this->openLoginModal();
        $notes .= $this->takeStepScreenshot(2, "Modal de login abierto");

        // 3️⃣ Credenciales
        $cedula = '27759045';
        $password = '12345678';

        $this->driver->findElement(WebDriverBy::id('ced'))->sendKeys($cedula);
        $this->driver->findElement(WebDriverBy::id('pass'))->sendKeys($password);
        $notes .= $this->takeStepScreenshot(3, "Credenciales ingresadas");

        // 4️⃣ Enviar
        $this->driver->findElement(WebDriverBy::cssSelector('input[name="iniciar"]'))->click();
        $notes .= $this->takeStepScreenshot(4, "Botón de login clickeado");

        sleep(2);

        // 5️⃣ Verificar que no exista mensaje de error
        $errorElement = $this->driver->findElement(WebDriverBy::id('loginError'));
        $hasError = strpos($errorElement->getAttribute('class'), 'd-none') === false;

        if ($hasError) {
            throw new \Exception("Error en login: " . $errorElement->getText());
        }

        return $notes;
    }

    public function testRegistroProducto()
    {
        $notes = "=== PRUEBA REGISTRO DE PRODUCTO ===\n";

        // 🔐 LOGIN
        $notes .= $this->login();

        // 5️⃣ Ir a módulo productos
        $this->driver->get($this->baseUrl . '/productos/producto');
        sleep(1);
        $notes .= $this->takeStepScreenshot(5, "Página de productos cargada");

        // 6️⃣ Abrir modal nuevo producto
        $this->driver->findElement(WebDriverBy::id('addProductBtn'))->click();
        sleep(1);
        $notes .= $this->takeStepScreenshot(6, "Modal de nuevo producto abierto");

        // 7️⃣ Completar formulario
        $nombre = "Producto nuevo selenium" . rand(100, 999);
        $precio = "20";
        $stock  = "15";
        $descripcion = "Descripcion automatica Selenium";

        $this->driver->findElement(WebDriverBy::id('nombre'))->sendKeys($nombre);
        $this->driver->findElement(WebDriverBy::id('precio'))->sendKeys($precio);
        $this->driver->findElement(WebDriverBy::id('productCategory'))->click();

        // Seleccionar primera categoría disponible
        $this->driver->findElement(WebDriverBy::cssSelector('#productCategory option:nth-child(2)'))->click();

        $this->driver->findElement(WebDriverBy::id('stock'))->sendKeys($stock);
        $this->driver->findElement(WebDriverBy::id('descripcion'))->sendKeys($descripcion);
        $imgPath = realpath(__DIR__ . '/public/imagen.png');
        $this->driver->findElement(WebDriverBy::id('productImage'))->sendKeys($imgPath);


        $notes .= $this->takeStepScreenshot(7, "Formulario completado ($nombre)");

        // 8️⃣ Guardar
        $this->driver->findElement(WebDriverBy::id('saveProductBtn'))->click();
        $notes .= $this->takeStepScreenshot(8, "Botón guardar clickeado");

        sleep(2);

        // 9️⃣ Verificar SweetAlert2 de éxito
        try {
            // Capturar el SweetAlert2
            $alert = $this->driver->findElement(WebDriverBy::cssSelector('.swal2-popup'));
            $alertText = $alert->getText();

            // Validar el mensaje real
            if (
                stripos($alertText, "Producto") !== false &&
                stripos($alertText, "Registrado correctamente") !== false
            ) {
                $notes .= " Producto registrado correctamente. SweetAlert detectado: $alertText\n";
                $this->reportTestResult('SGP-25', true, $notes);

            } else {
                $notes .= " SweetAlert apareció pero no indica éxito de registro: $alertText\n";
                $this->reportTestResult('SGP-25', false, $notes);
            }

        } catch (\Exception $e) {
            $notes .= " No apareció SweetAlert de confirmación\n";
            $this->reportTestResult('SGP-25', false, $notes);
        }

    }

    public function testRegistroProductoExistente()
    {
        $notes = "=== PRUEBA REGISTRO DE PRODUCTO YA EXISTENTE ===\n";

        // 🔐 Login
        $notes .= $this->login();

        // 1️⃣ Ir a módulo productos
        $this->driver->get($this->baseUrl . '/productos/producto');
        sleep(1);
        $notes .= $this->takeStepScreenshot(1, "Página de productos cargada");

        // 2️⃣ Abrir modal nuevo producto
        $this->driver->findElement(WebDriverBy::id('addProductBtn'))->click();
        sleep(1);
        $notes .= $this->takeStepScreenshot(2, "Modal nuevo producto abierto");

        // 3️⃣ Usar un nombre YA EXISTENTE
        $nombreExistente = "Producto Selenium";  // <-- AJUSTA AQUÍ el nombre real ya registrado
        $precio = "20";
        $stock  = "10";
        $descripcion = "Prueba de producto duplicado";

        $this->driver->findElement(WebDriverBy::id('nombre'))->sendKeys($nombreExistente);
        $this->driver->findElement(WebDriverBy::id('precio'))->sendKeys($precio);

        // Seleccionar primera categoría
        $this->driver->findElement(WebDriverBy::cssSelector('#productCategory option:nth-child(2)'))->click();

        $this->driver->findElement(WebDriverBy::id('stock'))->sendKeys($stock);
        $this->driver->findElement(WebDriverBy::id('descripcion'))->sendKeys($descripcion);

        $notes .= $this->takeStepScreenshot(3, "Formulario completado con un nombre ya existente");

        // 4️⃣ Guardar
        $this->driver->findElement(WebDriverBy::id('saveProductBtn'))->click();
        $notes .= $this->takeStepScreenshot(4, "Clic en guardar");

        sleep(2);

        // 5️⃣ Validar SweetAlert2 ERROR
        try {
            $alert = $this->driver->findElement(WebDriverBy::cssSelector('.swal2-popup'));
            $alertText = $alert->getText();

            if (stripos($alertText, "error") !== false ||
                stripos($alertText, "existe") !== false ||
                stripos($alertText, "ya") !== false) {

                $notes .= "Detección correcta: el sistema bloqueó el registro duplicado. SweetAlert: $alertText\n";
                $this->reportTestResult('SGP-26', true, $notes); // <-- ID del caso en TestLink
            } else {
                $notes .= "SweetAlert apareció pero no indica error por duplicado: $alertText\n";
                $this->reportTestResult('SGP-26', false, $notes);
            }

        } catch (\Exception $e) {
            $notes .= "No apareció SweetAlert de error al registrar un producto duplicado\n";
            $this->reportTestResult('SGP-26', false, $notes);
        }
    }

}
