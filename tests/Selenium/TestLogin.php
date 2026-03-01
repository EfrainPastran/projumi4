<?php
namespace Tests\Selenium;

use Facebook\WebDriver\WebDriverBy;

class TestLogin extends BaseTest
{
    private function takeStepScreenshot($stepNumber, $description)
    {
        $dir = __DIR__ . "/screenshots";
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $file = "$dir/step{$stepNumber}_" . time() . ".png";
        $this->driver->takeScreenshot($file);

        return "Paso $stepNumber: $description ✅ Screenshot: $file\n";
    }

    public function testLoginExitoso()
    {
        $notes = "";

        // 1️⃣ Navegar a la aplicación
        $this->driver->get($this->baseUrl.'/home/principal');
        $notes .= $this->takeStepScreenshot(1, "Página principal cargada");

        // 2️⃣ Abrir modal de login
        $this->openLoginModal();
        $notes .= $this->takeStepScreenshot(2, "Modal de login abierto");

        // 3️⃣ Ingresar credenciales válidas
        $cedula = '27759045';
        $password = '12345678';

        $cedulaField = $this->driver->findElement(WebDriverBy::id('ced'));
        $passwordField = $this->driver->findElement(WebDriverBy::id('pass'));
        $loginButton = $this->driver->findElement(WebDriverBy::cssSelector('input[name="iniciar"]'));

        $cedulaField->sendKeys($cedula);
        $passwordField->sendKeys($password);
        $notes .= $this->takeStepScreenshot(3, "Credenciales ingresadas: $cedula / $password");

        // 4️⃣ Click en "Iniciar sesión"
        $loginButton->click();
        $notes .= $this->takeStepScreenshot(4, "Botón de login clickeado");

        // 5️⃣ Verificar resultado
        sleep(2); // esperar respuesta

        $errorElement = $this->driver->findElement(WebDriverBy::id('loginError'));
        $hasError = strpos($errorElement->getAttribute('class'), 'd-none') === false;

        if (!$hasError) {
            $notes .= "✅ Login exitoso detectado\n";
            $this->reportTestResult('SGP-1', true, $notes);
        } else {
            $errorText = $errorElement->getText();
            $notes .= "❌ Error detectado: $errorText\n";
            $this->reportTestResult('SGP-1', false, $notes);
        }
    }

    public function testLoginFallido()
    {
        $notes = "";

        $this->driver->get($this->baseUrl.'/home/principal');
        $notes .= $this->takeStepScreenshot(1, "Página principal cargada");

        $this->openLoginModal();
        $notes .= $this->takeStepScreenshot(2, "Modal de login abierto");

        $cedula = '00000000';
        $password = 'wrong_password_12345';

        $cedulaField = $this->driver->findElement(WebDriverBy::id('ced'));
        $passwordField = $this->driver->findElement(WebDriverBy::id('pass'));
        $loginButton = $this->driver->findElement(WebDriverBy::cssSelector('input[name="iniciar"]'));

        $cedulaField->sendKeys($cedula);
        $passwordField->sendKeys($password);
        $notes .= $this->takeStepScreenshot(3, "Credenciales inválidas ingresadas: $cedula / $password");

        $loginButton->click();
        $notes .= $this->takeStepScreenshot(4, "Botón de login clickeado");

        sleep(2); // esperar respuesta

        $errorElement = $this->driver->findElement(WebDriverBy::id('loginError'));
        $errorDisplayed = $errorElement->isDisplayed() && strpos($errorElement->getAttribute('class'), 'd-none') === false;

        if ($errorDisplayed) {
            $errorText = $errorElement->getText();
            $notes .= "✅ Login fallido detectado: $errorText\n";
            $this->reportTestResult('SGP-5', true, $notes);
        } else {
            $notes .= "❌ No se detectó mensaje de error\n";
            $this->reportTestResult('SGP-5', false, $notes);
        }
    }
}
