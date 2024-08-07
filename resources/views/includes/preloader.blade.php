<style>
    * {
    border: 0;
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}
:root {
    --hue: 208;
    --bg: hsl(var(--hue),100%,100%); 
    --trans-dur: 0.3s; 
}
.preloader {
    position: fixed;
    left: 0;
    top: 0;
    background-color: var(--bg);
    color: var(--fg);
    display: flex;
    font: 1em/1.5 sans-serif;
    height: 100vh;
    transition: background-color var(--trans-dur), color var(--trans-dur);
    bottom: 0;
    right: 0;
    z-index: 99999;
    display: none;
} 

.preloader .pl {
    display: block;
    margin: auto;
    width: 16em;
    height: auto;
}
.preloader .pl line {
    animation-duration: 3s;
    animation-timing-function: ease-in-out;
    animation-iteration-count: infinite;
}
.preloader .pl__line1,
.preloader .pl__line9 {
    animation-name: line1;
}
.preloader .pl__line2,
.preloader .pl__line8 {
    animation-name: line2;
}
.preloader .pl__line3,
.preloader .pl__line7 {
    animation-name: line3;
}
.preloader .pl__line4,
.preloader .pl__line6 {
    animation-name: line4;
}
.preloader .pl__line5 {
    animation-name: line5;
}

/* Dark theme */
@media (prefers-color-scheme: dark) {
:root {
    --bg: hsl(var(--hue),90%,10%); 
}
}

/* Animations */
@keyframes line1 {
from,
8% {
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
18% {
    stroke-dashoffset: 16;
    transform: translate(0,8px);
}
28% {
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
38% {
    stroke-dashoffset: 0;
    transform: translate(0,0);
}
48% {
    opacity: 1;
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
53% {
    opacity: 0;
    stroke-dashoffset: 31.99;
    transform: translate(8px,16px);
}
56% {
    animation-timing-function: steps(1,start);
    opacity: 0;
    stroke-dashoffset: 32;
    transform: translate(0,16px);
}
60% {
    animation-timing-function: ease-out;
    opacity: 1;
    stroke-dashoffset: 32;
    transform: translate(0,16px);
}
70% {
    animation-timing-function: ease-in-out;
    stroke-dashoffset: 0;
    transform: translate(0,0);
}
80% {
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
90% {
    stroke-dashoffset: 16;
    transform: translate(0,8px);
}
to {
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
}
@keyframes line2 {
from,
6% {
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
16% {
    stroke-dashoffset: 16;
    transform: translate(0,8px);
}
26% {
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
36% {
    stroke-dashoffset: 0;
    transform: translate(0,0);
}
46% {
    opacity: 1;
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
51% {
    opacity: 0;
    stroke-dashoffset: 31.99;
    transform: translate(8px,16px);
}
54% {
    animation-timing-function: steps(1,start);
    opacity: 0;
    stroke-dashoffset: 32;
    transform: translate(0,16px);
}
58% {
    animation-timing-function: ease-out;
    opacity: 1;
    stroke-dashoffset: 32;
    transform: translate(0,16px);
}
68% {
    animation-timing-function: ease-in-out;
    stroke-dashoffset: 0;
    transform: translate(0,0);
}
78% {
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
88% {
    stroke-dashoffset: 16;
    transform: translate(0,8px);
}
98%,
to {
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
}
@keyframes line3 {
from,
4% {
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
14% {
    stroke-dashoffset: 16;
    transform: translate(0,8px);
}
24% {
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
34% {
    stroke-dashoffset: 0;
    transform: translate(0,0);
}
44% {
    opacity: 1;
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
49% {
    opacity: 0;
    stroke-dashoffset: 31.99;
    transform: translate(8px,16px);
}
52% {
    animation-timing-function: steps(1,start);
    opacity: 0;
    stroke-dashoffset: 32;
    transform: translate(0,16px);
}
56% {
    animation-timing-function: ease-out;
    opacity: 1;
    stroke-dashoffset: 32;
    transform: translate(0,16px);
}
66% {
    animation-timing-function: ease-in-out;
    stroke-dashoffset: 0;
    transform: translate(0,0);
}
76% {
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
86% {
    stroke-dashoffset: 16;
    transform: translate(0,8px);
}
96%,
to {
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
}
@keyframes line4 {
from,
2% {
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
12% {
    stroke-dashoffset: 16;
    transform: translate(0,8px);
}
22% {
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
32% {
    stroke-dashoffset: 0;
    transform: translate(0,0);
}
42% {
    opacity: 1;
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
47% {
    opacity: 0;
    stroke-dashoffset: 31.99;
    transform: translate(8px,16px);
}
50% {
    animation-timing-function: steps(1,start);
    opacity: 0;
    stroke-dashoffset: 32;
    transform: translate(0,16px);
}
54% {
    animation-timing-function: ease-out;
    opacity: 1;
    stroke-dashoffset: 32;
    transform: translate(0,16px);
}
64% {
    animation-timing-function: ease-in-out;
    stroke-dashoffset: 0;
    transform: translate(0,0);
}
74% {
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
84% {
    stroke-dashoffset: 16;
    transform: translate(0,8px);
}
94%,
to {
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
}
@keyframes line5 {
from {
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
10% {
    stroke-dashoffset: 16;
    transform: translate(0,8px);
}
20% {
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
30% {
    stroke-dashoffset: 0;
    transform: translate(0,0);
}
40% {
    stroke-dashoffset: -16;
    transform: translate(0,15px);
}
50% {
    stroke-dashoffset: -31;
    transform: translate(0,-48px);
}
58% {
    stroke-dashoffset: -31;
    transform: translate(0,8px);
}
65% {
    stroke-dashoffset: -31.99;
    transform: translate(0,-24px);
}
71.99% {
    animation-timing-function: steps(1);
    stroke-dashoffset: -31.99;
    transform: translate(0,-16px);
}
72% {
    animation-timing-function: ease-in-out;
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
82% {
    stroke-dashoffset: 16;
    transform: translate(0,8px);
}
92%,
to {
    stroke-dashoffset: 31.99;
    transform: translate(0,16px);
}
}
</style>

<div class="preloader">
    <svg class="pl" viewBox="0 0 128 128" width="128px" height="128px">
        <defs>
            <linearGradient id="pl-grad" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%" stop-color="#000" />
                <stop offset="100%" stop-color="#fff" />
            </linearGradient>
            <mask id="pl-mask">
                <rect x="0" y="0" width="128" height="128" fill="url(#pl-grad)" />
            </mask>
        </defs>
        <g stroke-linecap="round" stroke-width="8" stroke-dasharray="32 32">
            <g stroke="hsl(208,57%,25%)">
                <line class="pl__line1" x1="4" y1="48" x2="4" y2="80" />
                <line class="pl__line2" x1="19" y1="48" x2="19" y2="80" />
                <line class="pl__line3" x1="34" y1="48" x2="34" y2="80" />
                <line class="pl__line4" x1="49" y1="48" x2="49" y2="80" />
                <line class="pl__line5" x1="64" y1="48" x2="64" y2="80" />
                <g transform="rotate(180,79,64)">
                    <line class="pl__line6" x1="79" y1="48" x2="79" y2="80" />
                </g>
                <g transform="rotate(180,94,64)">
                    <line class="pl__line7" x1="94" y1="48" x2="94" y2="80" />
                </g>
                <g transform="rotate(180,109,64)">
                    <line class="pl__line8" x1="109" y1="48" x2="109" y2="80" />
                </g>
                <g transform="rotate(180,124,64)">
                    <line class="pl__line9" x1="124" y1="48" x2="124" y2="80" />
                </g>
            </g>
            <g stroke="hsl(82,67%,49%)" mask="url(#pl-mask)">
                <line class="pl__line1" x1="4" y1="48" x2="4" y2="80" />
                <line class="pl__line2" x1="19" y1="48" x2="19" y2="80" />
                <line class="pl__line3" x1="34" y1="48" x2="34" y2="80" />
                <line class="pl__line4" x1="49" y1="48" x2="49" y2="80" />
                <line class="pl__line5" x1="64" y1="48" x2="64" y2="80" />
                <g transform="rotate(180,79,64)">
                    <line class="pl__line6" x1="79" y1="48" x2="79" y2="80" />
                </g>
                <g transform="rotate(180,94,64)">
                    <line class="pl__line7" x1="94" y1="48" x2="94" y2="80" />
                </g>
                <g transform="rotate(180,109,64)">
                    <line class="pl__line8" x1="109" y1="48" x2="109" y2="80" />
                </g>
                <g transform="rotate(180,124,64)">
                    <line class="pl__line9" x1="124" y1="48" x2="124" y2="80" />
                </g>
            </g>
        </g>
    </svg>   
</div> 