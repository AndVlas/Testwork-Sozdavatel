
function setPlusIcon() {
    const allLi = document.querySelectorAll('.menu li');
    
    allLi.forEach(li => {
        const nestedUl = li.querySelector(':scope > ul');
        
        if (nestedUl) {
            li.classList.add('has-children');

            nestedUl.classList.add('hidden');
            
            const link = li.querySelector(':scope > a');
            if (link) {
                link.innerHTML = '[+] ' + link.innerHTML;
                link.onclick = aClick;
            }
        } else {
            const link = li.querySelector(':scope > a');
            if (link) {
                link.onclick = aClick;
            }
        }
    });
}

function aClick(event) {
    const link = event.currentTarget;
    
    console.log('Клик по ссылке:', link);
    
    const parentLi = link.parentElement;

    const nestedList = parentLi.querySelector(':scope > ul');

    if (!nestedList) {
        console.log('Переход по ссылке разрешён');
        return true;
    }
    
    console.log('Переход по ссылке запрещён');

    if (nestedList.classList.contains('hidden')) {

        nestedList.classList.remove('hidden');
        link.innerHTML = '[-] ' + link.innerHTML.substring(4);
    } else {

        nestedList.classList.add('hidden');
        link.innerHTML = '[+] ' + link.innerHTML.substring(4);
    }
    
    event.preventDefault();
    return false;
}

document.addEventListener('DOMContentLoaded', setPlusIcon);