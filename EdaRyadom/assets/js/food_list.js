const dataFile = '../../../assets/php/food-employees_request.php';

async function fetchData() {
    try {
        // Получаем данные
        const response = await fetch(dataFile);

        // Преобразуем ответ в JSON и вытаскиваем из JSON массива список с едой из файла с запросами
        const responseJSON = await response.json(); 
        const foodList = responseJSON['food_list'];
        
        // Возвращаем значение из функции
        return foodList
    } catch (error) {
        console.error('Пиздец:', error);
    }
}

// Вызов функции и получаем данные с него, которые потом преобразуем 
fetchData().then((foodList) => {

    // Переменные для захвата событий
    const addBtn = document.querySelector('#addProduct');
    const positionsList = document.querySelector('#editOrderList')
    const listElement = `
        <li class="create-order-item">
            <button class="delete-prodcut" type="button" data-action="deleteProduct">
                <svg width="10" height="4" viewBox="0 0 10 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1.5 1.5H7.5" stroke="white" stroke-width="3" stroke-linecap="round"/>
                </svg>
            </button>

            <select class="create-order-position" name="position[]">
                ${foodList.map((product) => `<option value="${product.food_id}">${product.name}</option>`)}
            </select>

            <input class="create-order-position amount" name="amount[]" type="number" min="1" max="20" value="1">
        </li>`;

    // Вставляем первый элемент при редактировании
    positionsList.insertAdjacentHTML('beforeend', listElement);

    // Добавление позиции
    addBtn.addEventListener('click', function(event){
        // Сброс стандартных настроек браузера для работы кнопок
        event.preventDefault();
        positionsList.insertAdjacentHTML('beforeend', listElement);
    });

    // Удаление позиции
    positionsList.addEventListener('click', function(e){
        e.preventDefault();

        // В кнопку вшит data-action="delete". С помощю closest() находим тот элемент, которому принадлежит кнопка удаления
        if (e.target.dataset.action === 'deleteProduct'){
            const foodPosition = e.target.closest('li');
            foodPosition.remove();
        }
    });
});