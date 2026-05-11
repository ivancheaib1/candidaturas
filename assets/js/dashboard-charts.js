// Configuración global de Chart.js 4.x
Chart.defaults.font.family = "'Lato', sans-serif";
Chart.defaults.font.size = 14;
Chart.defaults.color = '#666';
Chart.defaults.borderColor = '#e0e0e0';

// Función auxiliar para convertir RGB a valores de Chart
function rgbToChartColor(r, g, b, alpha = 0.5) {
  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

// Colores predefinidos
const chartColors = {
  yes: rgbToChartColor(40, 167, 69, 0.7),      // Verde
  no: rgbToChartColor(220, 53, 69, 0.7),       // Rojo
  primary: '#007BFF',
  success: '#28A745',
  danger: '#DC3545',
  info: '#17A2B8',
  warning: '#FFC107',
  dark: '#343A40'
};

// Paleta de colores para múltiples series
const chartPalette = [
  '#FF6384',
  '#36A2EB',
  '#FFCE56',
  '#4BC0C0',
  '#9966FF',
  '#FF9F40',
  '#FF6384',
  '#C9CBCF'
];

// Opciones responsivas comunes
const responsiveChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom',
      labels: {
        padding: 15,
        font: {
          size: 12,
          weight: 'bold'
        },
        usePointStyle: true
      }
    }
  }
};
