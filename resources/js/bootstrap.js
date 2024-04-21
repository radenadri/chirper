import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import intersect from '@alpinejs/intersect'

Alpine.plugin(intersect)

Livewire.start()
