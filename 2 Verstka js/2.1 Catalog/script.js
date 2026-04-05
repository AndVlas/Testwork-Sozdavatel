import { products } from './data.js';

let currentView = "tile";

const catalog = document.getElementById("catalog");
const toggleBtn = document.getElementById("toggleView");

// Отрисовка товаров
function render() {
    catalog.innerHTML = "";
    
    products.forEach(product => {
        const div = document.createElement("div");
        div.className = "product";
        div.innerHTML = `<h3>${product.name}</h3><p>${product.price}</p>`;
        catalog.appendChild(div);
    });
    
    // Меняем класс в зависимости от режима
    if (currentView === "tile") {
        catalog.className = "catalog catalog--tile";
        toggleBtn.textContent = "Список";
    } else {
        catalog.className = "catalog catalog--list";
        toggleBtn.textContent = "Плитка";
    }
}

// Переключение вида
function toggleView() {
    currentView = currentView === "tile" ? "list" : "tile";
    render();
}

toggleBtn.onclick = toggleView;

render();