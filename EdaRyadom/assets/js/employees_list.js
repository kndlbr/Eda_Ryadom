
const dataFile = '../../../assets/php/food-employees_request.php';

async function fetchData() {
    try {
        // Получаем данные
        const response = await fetch(dataFile);

        // Преобразуем ответ в JSON и вытаскиваем из JSON массива список с сотрудниками из файла с запросами
        const responseJSON = await response.json();
        const employeesList = responseJSON['employees_list'];
        
        // Возвращаем значение из функции
        return employeesList
    } catch (error) {
        console.error('Пиздец:', error);
    }
}

// Вызов функции и получаем данные с него, которые потом преобразуем 
fetchData().then((employeesList) => {

    // Переменные для захвата событий
    const addBtn = document.querySelector('#addEmployee');
    const workersList = document.querySelector('#editShiftList')
    const listElement = `

        <li class="create-order-item">
            <button class="delete-prodcut" type="button" data-action="deleteEmployee">
                <svg width="10" height="4" viewBox="0 0 10 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1.5 1.5H7.5" stroke="white" stroke-width="3" stroke-linecap="round"/>
                </svg>
            </button>

            <select class="create-order-position" name="employees[]">
                ${employeesList.map((employee) => `<option value="${employee.user_id}">${employee.lname}&nbsp;${employee.role_name}</option>`)}
            </select>
        </li>`;

    // Вставляем первый элемент при редактировании
    workersList.insertAdjacentHTML('beforeend', listElement);

    // Добавление сотрудника
    addBtn.addEventListener('click', function(event){
        // Сброс стандартных настроек браузера для работы кнопок
        event.preventDefault();
        workersList.insertAdjacentHTML('beforeend', listElement);
    });

    // Удаление сотрудника
    workersList.addEventListener('click', function(e){
        e.preventDefault();

        // В кнопку вшит data-action="delete". С помощю closest() находим тот элемент, которому принадлежит кнопка удаления
        if (e.target.dataset.action === 'deleteEmployee'){
            const employeePosition = e.target.closest('li');
            employeePosition.remove();
        }
    });
});