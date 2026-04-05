// ========== Работа с состоянием формы ==========

function getFormState(form) {
    const state = {};
    
    for (let el of form.elements) {
        if (el.type === 'radio') {
            if (el.checked) state[el.name] = el.value;
        }
        else if (el.type === 'checkbox') {
            if (!state[el.name]) {
                state[el.name] = Array.from(form.querySelectorAll(`input[name="${el.name}"]:checked`)).map(cb => cb.value);
            }
        }
        else {
            state[el.name] = el.value;
        }
    }
    return state;
}

function setFormState(form, state) {
    for (let [name, value] of Object.entries(state)) {
        const elements = form.querySelectorAll(`[name="${name}"]`);
        if (!elements.length) continue;
        
        const first = elements[0];
        
        if (first.type === 'radio') {
            elements.forEach(r => r.checked = r.value === value);
        }
        else if (first.type === 'checkbox') {
            const values = Array.isArray(value) ? value : [];
            elements.forEach(cb => cb.checked = values.includes(cb.value));
        }
        else {
            first.value = value;
        }
    }
}

// ========== Управление историей ==========

function createUndoManager(form, onUpdate) {
    let history = [];
    let index = -1;
    
    const saveState = () => {
        
        const newState = getFormState(form);

        if (history[index] && JSON.stringify(history[index]) === JSON.stringify(newState)) return;
        
        history = [...history.slice(0, index + 1), newState];
        index++;
        
        onUpdate?.({ canUndo: index > 0 });
    };
    
    const undo = () => {
        if (index <= 0) return false;
        
        setFormState(form, history[--index]);
        
        onUpdate?.({ canUndo: index > 0 });
        return true;
    };
    
    history = [getFormState(form)];
    index = 0;
    
    form.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('change', saveState);
    });
    
    onUpdate?.({ canUndo: false });
    
    return { undo };
}

// ========== Инициализация ==========

const form = document.getElementById('mainForm');
const undoButton = document.getElementById('undoButton');

const undoManager = createUndoManager(form, ({ canUndo }) => {
    undoButton.disabled = !canUndo;
});

undoButton.addEventListener('click', () => undoManager.undo());