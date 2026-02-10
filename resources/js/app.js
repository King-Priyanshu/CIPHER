import './bootstrap';

import Alpine from 'alpinejs';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.FullCalendar = {
    Calendar,
    dayGridPlugin,
    interactionPlugin
};
window.Chart = Chart;

// Alpine.js directives for animations and micro-interactions
Alpine.directive('animate', (el, { value }) => {
    el.classList.add('page-transition');
});

Alpine.directive('hover-lift', (el) => {
    el.addEventListener('mouseenter', () => {
        el.style.transform = 'translateY(-2px)';
        el.style.transition = 'transform 0.2s ease';
    });
    
    el.addEventListener('mouseleave', () => {
        el.style.transform = 'translateY(0)';
    });
});

Alpine.directive('fade-in', (el) => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    
    setTimeout(() => {
        el.style.opacity = '1';
        el.style.transform = 'translateY(0)';
    }, 100);
});

Alpine.start();
