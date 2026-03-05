document.addEventListener('DOMContentLoaded', () => {
    // Configuración para el modal de registro
    let currentStep = 0;
    const steps = document.querySelectorAll('#modalAgregar .form-step');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');
    const progressBar = document.querySelector('#modalAgregar .progress-bar');

    function showStep(n) {
        steps.forEach((step, index) => {
            step.classList.toggle('d-none', index !== n);
        });

        // Actualizar botones
        prevBtn.style.display = n === 0 ? 'none' : 'inline-block';
        nextBtn.style.display = n === steps.length - 1 ? 'none' : 'inline-block';
        submitBtn.classList.toggle('d-none', n !== steps.length - 1);
        
        // Actualizar barra de progreso
        const progress = ((n + 1) / steps.length) * 100;
        progressBar.style.width = `${progress}%`;
        progressBar.setAttribute('aria-valuenow', progress);
        progressBar.textContent = `${Math.round(progress)}%`;
    }

    function nextPrev(n) {
        if (n === 1 && !validateForm(currentStep)) return false;

        currentStep += n;
        if (currentStep >= steps.length) currentStep = steps.length - 1;
        if (currentStep < 0) currentStep = 0;

        showStep(currentStep);
    }

    function validateForm(step) {
        let isValid = true;
        // Seleccionamos los campos visibles del paso actual
        const currentStepFields = steps[step].querySelectorAll('input, select, textarea');
        
        currentStepFields.forEach(field => {
            // Ejecutamos la validación de tu objeto global Validaciones
            if (!Validaciones.validarCampo(field)) {
                isValid = false;
            }
        });

        if (!isValid) {
            // Usamos SweetAlert para un look más moderno, ya que lo tienes en el proyecto
            Swal.fire({
                icon: 'error',
                title: 'Campos incompletos',
                text: 'Por favor, corrija los errores en rojo antes de continuar.',
                confirmButtonColor: '#0d6efd'
            });
        }

        return isValid;
    }
    // Asignar eventos a los botones
    if (nextBtn) nextBtn.addEventListener('click', () => nextPrev(1));
    if (prevBtn) prevBtn.addEventListener('click', () => nextPrev(-1));

    // Configuración para el modal de edición
    let editCurrentStep = 0;
    const editSteps = document.querySelectorAll('#modalEditar .form-step');
    const editNextBtn = document.getElementById('edit_nextBtn');
    const editPrevBtn = document.getElementById('edit_prevBtn');
    const editSubmitBtn = document.getElementById('edit_submitBtn');
    const editProgressBar = document.querySelector('#modalEditar .progress-bar');

    function showEditStep(n) {
        editSteps.forEach((step, index) => {
            step.classList.toggle('d-none', index !== n);
        });

        // Actualizar botones
        editPrevBtn.style.display = n === 0 ? 'none' : 'inline-block';
        editNextBtn.style.display = n === editSteps.length - 1 ? 'none' : 'inline-block';
        editSubmitBtn.classList.toggle('d-none', n !== editSteps.length - 1);
        
        // Actualizar barra de progreso
        const progress = ((n + 1) / editSteps.length) * 100;
        editProgressBar.style.width = `${progress}%`;
        editProgressBar.setAttribute('aria-valuenow', progress);
        editProgressBar.textContent = `${Math.round(progress)}%`;
    }

    function editNextPrev(n) {
        if (n === 1 && !validateEditForm(editCurrentStep)) return false;

        editCurrentStep += n;
        if (editCurrentStep >= editSteps.length) editCurrentStep = editSteps.length - 1;
        if (editCurrentStep < 0) editCurrentStep = 0;

        showEditStep(editCurrentStep);
    }

    function validateEditForm(step) {
        let isValid = true;
        const currentStepFields = editSteps[step].querySelectorAll('[required]');
        
        currentStepFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            alert('Por favor complete todos los campos requeridos antes de continuar.');
        }

        return isValid;
    }

    // Asignar eventos a los botones
    if (editNextBtn) editNextBtn.addEventListener('click', () => editNextPrev(1));
    if (editPrevBtn) editPrevBtn.addEventListener('click', () => editNextPrev(-1));
});
