let transactions = [];

const formatMoney = amount =>
    `${amount.toLocaleString("en-GB", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })} KM`;


async function showPage(pageId) {
    const validPages = [
        "dashboard",
        "transactions",
        "budgets",
        "reports",
        "profile"
    ];

    if (!validPages.includes(pageId)) {
        pageId = "dashboard";
    }

    const viewContainer = document.getElementById("view-container");

    const titles = {
        dashboard: "Dashboard",
        transactions: "Transactions",
        budgets: "Budgets",
        reports: "Reports",
        profile: "Profile & Settings"
    };

    try {
        const response = await fetch(`views/${pageId}.html`);

        if (!response.ok) {
            throw new Error(
                `Failed to load views/${pageId}.html. Status: ${response.status}`
            );
        }

        const html = await response.text();
        viewContainer.innerHTML = html;

        // Each view is fetched by itself, so it must not keep the hidden
        // class used when views were originally shown together on one page.
        viewContainer.querySelectorAll(".page-section").forEach(section => {
            section.classList.remove("hidden");
        });

        document.getElementById("page-title").textContent =
            titles[pageId];

        document.querySelectorAll(".nav-link").forEach(link => {
            link.classList.toggle(
                "active",
                link.dataset.page === pageId
            );
        });

        history.replaceState(null, "", `#${pageId}`);

        initializePage(pageId);

        window.scrollTo({ top: 0, behavior: "smooth" });

    } catch (error) {
        console.error("View loading error:", error);
        viewContainer.innerHTML =
            "<p>Unable to load this page. Check the view file path.</p>";
    }
}

document.addEventListener("click", event => {
    const link = event.target.closest("[data-page]");

    if (link) {
        event.preventDefault();
        showPage(link.dataset.page);
    }
});


function renderTransactions() {
    const recentBody = document.getElementById("recent-transactions");
    const allBody = document.getElementById("all-transactions");

    if (recentBody) recentBody.replaceChildren();
    if (allBody) allBody.replaceChildren();

    const addRow = (transaction, target) => {
        const row = document.createElement("tr");

        const nameCell = document.createElement("td");
        const dateCell = document.createElement("td");
        const categoryCell = document.createElement("td");
        const amountCell = document.createElement("td");

        nameCell.textContent = transaction.name;
        dateCell.textContent = transaction.date;
        categoryCell.textContent = transaction.category;

        amountCell.textContent =
            `${transaction.type === "income" ? "+" : "-"}${formatMoney(transaction.amount)}`;

        amountCell.className =
            transaction.type === "income"
                ? "amount-income"
                : "amount-expense";

        row.append(nameCell, dateCell, categoryCell, amountCell);
        target.appendChild(row);
    };

    if (allBody) {
        transactions.forEach(transaction => {
            addRow(transaction, allBody);
        });
    }

    if (recentBody) {
        transactions.slice(-4).reverse().forEach(transaction => {
            addRow(transaction, recentBody);
        });
    }
}



function updateDashboard() {
    const balanceElement = document.getElementById("balance");
    const incomeElement = document.getElementById("income");
    const expensesElement = document.getElementById("expenses");

    // The dashboard might not be the currently loaded view.
    if (!balanceElement || !incomeElement || !expensesElement) {
        return;
    }

    const currentMonth = new Date().toISOString().slice(0, 7);

    const monthlyTransactions = transactions.filter(transaction =>
        transaction.date.startsWith(currentMonth)
    );

    const income = monthlyTransactions
        .filter(transaction => transaction.type === "income")
        .reduce((sum, transaction) => sum + transaction.amount, 0);

    const expenses = monthlyTransactions
        .filter(transaction => transaction.type === "expense")
        .reduce((sum, transaction) => sum + transaction.amount, 0);

    incomeElement.textContent = formatMoney(income);
    expensesElement.textContent = formatMoney(expenses);
    balanceElement.textContent = formatMoney(income - expenses);
}


function handleTransactionSubmit(event) {
    event.preventDefault();

    const nameInput = document.getElementById("transaction-name");
    const amountInput = document.getElementById("transaction-amount");

    const name = nameInput.value.trim();
    const amount = Number(amountInput.value);

    if (!name || !Number.isFinite(amount) || amount <= 0) {
        alert("Enter a description and an amount greater than zero.");
        return;
    }

    const today = new Date();

    const date = [
        today.getFullYear(),
        String(today.getMonth() + 1).padStart(2, "0"),
        String(today.getDate()).padStart(2, "0")
    ].join("-");

    transactions.push({
        name,
        amount,
        date,
        type: document.getElementById("transaction-type").value,
        category: document.getElementById("transaction-category").value
    });

    renderTransactions();
    updateDashboard();

    event.target.reset();

    alert("Demo transaction added successfully!");
}

function saveProfile() {
    const name = document.getElementById("display-name").value.trim();
    const message = document.getElementById("profile-message");

    if (!name) {
        message.textContent = "Please enter a display name.";
        return;
    }

    const sidebarName = document.querySelector(".sidebar-bottom strong");

    if (sidebarName) {
        sidebarName.textContent = name;
    }

    message.textContent =
        "Demo preferences updated. They will reset when you refresh.";
}


function initializePage(pageId) {
    if (pageId === "dashboard") {
        renderTransactions();
        updateDashboard();
    }

    if (pageId === "transactions") {
        renderTransactions();

        const form = document.getElementById("transaction-form");

        if (form) {
            form.addEventListener("submit", handleTransactionSubmit);
        }
    }

    if (pageId === "profile") {
        const saveButton = document.getElementById("save-profile");

        if (saveButton) {
            saveButton.addEventListener("click", saveProfile);
        }
    }
}


window.addEventListener("hashchange", () => {
    const pageId = location.hash.slice(1) || "dashboard";
    showPage(pageId);
});

async function startApp() {
    try {
        const response = await fetch("assets/data/sampledata.json");

        if (!response.ok) {
            throw new Error("Could not load sampledata.json");
        }

        transactions = await response.json();

    } catch (error) {
        console.error("Error loading transaction data:", error);
        transactions = [];
    }

    const initialPage = location.hash.slice(1) || "dashboard";
    await showPage(initialPage);
}

startApp();
