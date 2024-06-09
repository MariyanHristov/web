document.querySelector('form').addEventListener('submit', function (e) {
    e.preventDefault();
    
    var formData = new FormData(this);

    fetch('upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            loadTable(data.file);
        } else {
            alert('File upload failed!');
        }
    });
});

function loadTable(file) {
    fetch('process.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ action: 'load', file: file })
    })
    .then(response => response.json())
    .then(data => {
        displayTable(data);
    });
}

function displayTable(data) {
    const tableContainer = document.getElementById('table-container');
    tableContainer.innerHTML = '';

    const table = document.createElement('table');
    data.forEach(row => {
        const tr = document.createElement('tr');
        for (let cell in row) {
            const td = document.createElement('td');
            td.textContent = row[cell];
            tr.appendChild(td);
        }
        table.appendChild(tr);
    });

    tableContainer.appendChild(table);
}

function performAction(action) {
    const file = document.querySelector('input[type="file"]').files[0];
    fetch('process.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ action: action, file: file.name })
    })
    .then(response => response.json())
    .then(data => {
        displayTable(data);
    });
}

function exportTable() {
    const data = [...document.querySelectorAll('table tr')].map(tr =>
        [...tr.children].map(td => td.textContent)
    );

    fetch('export.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ export: true, data: JSON.stringify(data), format: 'csv' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.href = 'exports/' + data.file;
        }
    });
}
