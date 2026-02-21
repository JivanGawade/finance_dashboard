fetch('get_chart_data.php')
    .then(res => res.json())
    .then(data => {
        const ctx = document.getElementById('expenseChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.map(i => i.category),
                datasets: [{ data: data.map(i => i.total), backgroundColor: ['#4361ee', '#2ec4b6', '#e71d36'] }]
            }
        });
    });