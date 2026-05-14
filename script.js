function showSection(sectionId) {
    let sections = document.getElementsByClassName('module-section');
    for (let i = 0; i < sections.length; i++) {
        sections[i].style.display = 'none';
    }
    const target = document.getElementById(sectionId);
    if(target) target.style.display = 'block';
}

let cart = {};

function punchOrder(itemName, itemPrice) {
    // 1. Add item to cart or increase quantity
    if (cart[itemName]) {
        cart[itemName].qty += 1;
    } else {
        cart[itemName] = { price: parseFloat(itemPrice), qty: 1 };
    }

    // 2. Update the transaction summary and receipt
    updateTransactionSummary();
    updateReceipt();
}

function updateReceipt() {
    const receiptContainer = document.getElementById("receipt-items");
    const totalElement = document.getElementById("receipt-total-price");
    
    // Clear current view
    receiptContainer.innerHTML = "";
    let total = 0;

    // Loop through the cart memory and output the lines
    for (const [name, data] of Object.entries(cart)) {
        const subtotal = data.qty * data.price;
        total += subtotal; 

        const itemRow = document.createElement("div");
        itemRow.className = "receipt-item";
        itemRow.innerHTML = `
            <div style="display: flex; flex: 1;">
                <span class="receipt-item-qty">${data.qty}</span>
                <span class="receipt-item-name">${name}</span>
            </div>
            <span>₱${subtotal.toFixed(2)}</span>
        `;
        receiptContainer.appendChild(itemRow);
    }

    // Update the final total at the bottom right
    if(totalElement) {
        totalElement.innerText = `₱${total.toFixed(2)}`;
    }
}

function updateTransactionSummary() {
    const container = document.getElementById("transaction-items");
    container.innerHTML = "";
    let total = 0;

    for (const [name, data] of Object.entries(cart)) {
        const subtotal = data.qty * data.price;
        total += subtotal;
        const itemDiv = document.createElement("div");
        itemDiv.innerHTML = `${data.qty}x ${name} - ₱${subtotal.toFixed(2)}`;
        container.appendChild(itemDiv);
    }

    const totalDiv = document.createElement("div");
    totalDiv.innerHTML = `<strong>Total: ₱${total.toFixed(2)}</strong>`;
    container.appendChild(totalDiv);
}