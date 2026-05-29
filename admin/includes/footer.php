    </div> <!-- container -->
    
    <script>
        // Функции для открытия модальных окон услуг
        function openAddServiceModal() {
            document.getElementById('addServiceModal').style.display = 'flex';
        }
        
        function editService(id, title, image) {
            document.getElementById('edit_service_id').value = id;
            document.getElementById('edit_service_title').value = title;
            document.getElementById('edit_current_image').src = image;
            document.getElementById('edit_service_url').value = '';
            const editFileInput = document.querySelector('#editServiceModal input[name="service_image_file"]');
            const editPreviewDiv = document.getElementById('editImagePreview');
            if (editFileInput) editFileInput.value = '';
            if (editPreviewDiv) editPreviewDiv.style.display = 'none';
            document.getElementById('editServiceModal').style.display = 'flex';
        }
        
        // Функции для открытия модальных окон компетенций
        function openAddCompetencyModal() {
            document.getElementById('addCompetencyModal').style.display = 'flex';
        }
        
        function editCompetency(id, title) {
            document.getElementById('edit_competency_id').value = id;
            document.getElementById('edit_competency_title').value = title;
            document.getElementById('editCompetencyModal').style.display = 'flex';
        }
        
        // Предпросмотр изображения для добавления услуги
        const addFileInput = document.querySelector('#addServiceModal input[name="service_image_file"]');
        const addUrlInput = document.querySelector('#addServiceModal input[name="service_image_url"]');
        const addPreviewDiv = document.getElementById('addImagePreview');
        const addPreviewImg = document.getElementById('addPreviewImg');
        
        if (addFileInput) {
            addFileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        if (addPreviewImg) addPreviewImg.src = event.target.result;
                        if (addPreviewDiv) addPreviewDiv.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                    if (addUrlInput) addUrlInput.value = '';
                }
            });
        }
        
        if (addUrlInput) {
            addUrlInput.addEventListener('input', function(e) {
                if (e.target.value) {
                    if (addPreviewImg) addPreviewImg.src = e.target.value;
                    if (addPreviewDiv) addPreviewDiv.style.display = 'block';
                    if (addFileInput) addFileInput.value = '';
                } else if (addPreviewDiv) {
                    addPreviewDiv.style.display = 'none';
                }
            });
        }
        
        // Предпросмотр для редактирования услуги
        const editFileInput = document.querySelector('#editServiceModal input[name="service_image_file"]');
        const editUrlInput = document.querySelector('#editServiceModal input[name="service_image_url"]');
        const editPreviewDiv = document.getElementById('editImagePreview');
        const editPreviewImg = document.getElementById('editPreviewImg');
        
        if (editFileInput) {
            editFileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        if (editPreviewImg) editPreviewImg.src = event.target.result;
                        if (editPreviewDiv) editPreviewDiv.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                    if (editUrlInput) editUrlInput.value = '';
                }
            });
        }
        
        if (editUrlInput) {
            editUrlInput.addEventListener('input', function(e) {
                if (e.target.value) {
                    if (editPreviewImg) editPreviewImg.src = e.target.value;
                    if (editPreviewDiv) editPreviewDiv.style.display = 'block';
                    if (editFileInput) editFileInput.value = '';
                }
            });
        }
        
        // Закрытие модальных окон при клике вне
        window.onclick = function(event) {
            const addServiceModal = document.getElementById('addServiceModal');
            const editServiceModal = document.getElementById('editServiceModal');
            const addCompetencyModal = document.getElementById('addCompetencyModal');
            const editCompetencyModal = document.getElementById('editCompetencyModal');
            const addClientModal = document.getElementById('addClientModal');
            const editClientModal = document.getElementById('editClientModal');
            if (event.target === addServiceModal) addServiceModal.style.display = 'none';
            if (event.target === editServiceModal) editServiceModal.style.display = 'none';
            if (event.target === addCompetencyModal) addCompetencyModal.style.display = 'none';
            if (event.target === editCompetencyModal) editCompetencyModal.style.display = 'none';
            if (event.target === addClientModal) addClientModal.classList.remove('show');
            if (event.target === editClientModal) editClientModal.classList.remove('show');
        }
    </script>
    <script src="/assets/js/admin.js"></script>
</body>
</html>