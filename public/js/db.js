document.addEventListener('DOMContentLoaded', function() {
    // Datos de ejemplo para el historial
    const historyData = [
        {
            date: '2023-11-15 14:30:22',
            type: 'Respaldo',
            file: 'backup_20231115.sql',
            size: '45.2 MB',
            status: 'Completado'
        },
        {
            date: '2023-11-10 09:15:45',
            type: 'Restauración',
            file: 'backup_20231105.sql',
            size: '42.8 MB',
            status: 'Completado'
        },
        {
            date: '2023-11-05 18:20:33',
            type: 'Respaldo',
            file: 'backup_20231105.sql',
            size: '42.8 MB',
            status: 'Completado'
        },
        {
            date: '2023-10-28 11:05:12',
            type: 'Restauración',
            file: 'backup_20231020.sql',
            size: '40.1 MB',
            status: 'Fallido'
        },
        {
            date: '2023-10-20 16:45:18',
            type: 'Respaldo',
            file: 'backup_20231020.sql',
            size: '40.1 MB',
            status: 'Completado'
        }
    ];

    // Llenar el historial
    const historyTableBody = document.getElementById('historyTableBody');
    
    historyData.forEach(item => {
        const row = document.createElement('tr');
        
        let statusClass = '';
        if (item.status === 'Completado') statusClass = 'status-success';
        else if (item.status === 'Fallido') statusClass = 'status-error';
        else statusClass = 'status-warning';
        
        row.innerHTML = `
            <td>${item.date}</td>
            <td>${item.type}</td>
            <td>${item.file}</td>
            <td>${item.size}</td>
            <td><span class="status-badge ${statusClass}">${item.status}</span></td>
        `;
        
        historyTableBody.appendChild(row);
    });

    // Llenar el select de archivos de respaldo
    const backupFiles = [
        'backup_20231115.sql',
        'backup_20231105.sql',
        'backup_20231020.sql',
        'backup_20231010.sql'
    ];
    
    const backupFileSelect = document.getElementById('backupFile');
    
    backupFiles.forEach(file => {
        const option = document.createElement('option');
        option.value = file;
        option.textContent = file;
        backupFileSelect.appendChild(option);
    });

    // Confirmar respaldo
    document.getElementById('confirmBackup').addEventListener('click', function() {
        const backupModal = bootstrap.Modal.getInstance(document.getElementById('confirmBackupModal'));
        backupModal.hide();
        
        showProgressModal('Respaldo en progreso', 'Creando respaldo de la base de datos...');
        
        // Simular proceso de respaldo
        simulateOperation('backup');
    });

    // Confirmar restauración
    document.getElementById('confirmRestore').addEventListener('click', function() {
        const selectedFile = backupFileSelect.value;
        
        if (!selectedFile) {
            Swal.fire({
                title: '<span class="fw-bold">Selecciona un archivo</span>',
                text: 'Selecciona un archivo para restaurar',
                icon: 'warning',
                confirmButtonText: 'Aceptar',
                customClass: {
                confirmButton: 'btn btn-success px-4 fw-bold',
                popup: 'rounded-4 shadow',
                title: 'fs-4',
                icon: 'mt-2'
                },
                buttonsStyling: false,
                background: '#f8f9fa'
            });
            return;
        }
        
        const restoreModal = bootstrap.Modal.getInstance(document.getElementById('confirmRestoreModal'));
        restoreModal.hide();
        
        showProgressModal('Restauración en progreso', `Restaurando desde ${selectedFile}...`);
        
        // Simular proceso de restauración
        simulateOperation('restore');
    });

    // Mostrar modal de progreso
    function showProgressModal(title, message) {
        document.getElementById('progressModalTitle').textContent = title;
        document.getElementById('progressText').textContent = message;
        document.getElementById('operationProgress').style.width = '0%';
        
        const progressModal = new bootstrap.Modal(document.getElementById('progressModal'));
        progressModal.show();
    }

    // Simular operación (en un caso real sería una llamada AJAX)
    function simulateOperation(type) {
        let progress = 0;
        const progressBar = document.getElementById('operationProgress');
        const progressText = document.getElementById('progressText');
        
        const interval = setInterval(() => {
            progress += Math.random() * 10;
            if (progress > 100) progress = 100;
            
            progressBar.style.width = `${progress}%`;
            
            if (type === 'backup') {
                progressText.textContent = `Respaldo en progreso: ${Math.floor(progress)}% completado`;
            } else {
                progressText.textContent = `Restauración en progreso: ${Math.floor(progress)}% completado`;
            }
            
            if (progress === 100) {
                clearInterval(interval);
                
                setTimeout(() => {
                    const progressModal = bootstrap.Modal.getInstance(document.getElementById('progressModal'));
                    progressModal.hide();
                    
                    // Agregar al historial
                    const now = new Date();
                    const dateStr = now.toISOString().replace('T', ' ').substring(0, 19);
                    
                    const newEntry = {
                        date: dateStr,
                        type: type === 'backup' ? 'Respaldo' : 'Restauración',
                        file: type === 'backup' ? `backup_${now.getFullYear()}${(now.getMonth()+1).toString().padStart(2, '0')}${now.getDate().toString().padStart(2, '0')}.sql` : backupFileSelect.value,
                        size: type === 'backup' ? `${(Math.random() * 10 + 35).toFixed(1)} MB` : document.querySelector(`option[value="${backupFileSelect.value}"]`).previousElementSibling.textContent.split(' ')[1],
                        status: 'Completado'
                    };
                    
                    addHistoryEntry(newEntry);
                    
                    // Mostrar alerta de éxito
                    Swal.fire({
                        title: '<span class="fw-bold">Respaldo completado</span>',
                        text: 'Respaldo completado exitosamente',
                        icon: 'success',
                        confirmButtonText: 'Aceptar',
                        customClass: {
                            confirmButton: 'btn btn-success px-4 fw-bold',
                            popup: 'rounded-4 shadow',
                            title: 'fs-4',
                            icon: 'mt-2'
                        },
                        buttonsStyling: false,
                        background: '#f8f9fa'
                    }); 
                }, 500);
            }
        }, 300);
    }

    // Agregar entrada al historial
    function addHistoryEntry(entry) {
        const row = document.createElement('tr');
        
        row.innerHTML = `
            <td>${entry.date}</td>
            <td>${entry.type}</td>
            <td>${entry.file}</td>
            <td>${entry.size}</td>
            <td><span class="status-badge status-success">${entry.status}</span></td>
        `;
        
        historyTableBody.insertBefore(row, historyTableBody.firstChild);
    }
});