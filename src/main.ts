// ════════════════════════════════════════════
//  Gallery Frontend — main.ts
//  Browser-only, no module system required.
// ════════════════════════════════════════════

// ── Constants ──────────────────────────────

const PAGE_SIZE = 48;

// ── State ──────────────────────────────────

let allImages: ImageItem[]      = [];
let filteredImages: ImageItem[] = [];
let currentTopic: string        = "ALL";
let currentTag: string | null   = null;
let currentPage: number         = 1;
let lightboxIndex: number       = -1;

// ── DOM references ─────────────────────────

const topicSelect   = document.getElementById("topic-select")     as HTMLSelectElement;
const tagFilters    = document.getElementById("tag-filters")      as HTMLDivElement;
const imageGrid     = document.getElementById("image-grid")       as HTMLDivElement;
const pagination    = document.getElementById("pagination")       as HTMLDivElement;
const countText     = document.getElementById("count-text")       as HTMLSpanElement;
const emptyState    = document.getElementById("empty-state")      as HTMLDivElement;
const lightbox      = document.getElementById("lightbox")         as HTMLDivElement;
const lightboxImg   = document.getElementById("lightbox-img")     as HTMLImageElement;
const lightboxMeta  = document.getElementById("lightbox-meta")    as HTMLDivElement;
const lightboxClose = document.getElementById("lightbox-close")   as HTMLButtonElement;
const lightboxPrev  = document.getElementById("lightbox-prev")    as HTMLButtonElement;
const lightboxNext  = document.getElementById("lightbox-next")    as HTMLButtonElement;
const lightboxBdrop = document.getElementById("lightbox-backdrop") as HTMLDivElement;

// ── Init ───────────────────────────────────

function init(): void {
    const data: GalleryData = window.__GALLERY_DATA__ || { topics: [] };

    allImages = flattenImages(data);
    sortByCreated(allImages);

    applyFilters(data);
    renderTagFilters(data);

    topicSelect.addEventListener("change", () => {
        currentTopic = topicSelect.value;
        currentTag   = null;
        currentPage  = 1;
        applyFilters(data);
        renderTagFilters(data);
    });

    lightboxClose.addEventListener("click", closeLightbox);
    lightboxBdrop.addEventListener("click", closeLightbox);
    lightboxPrev.addEventListener("click",  () => navigateLightbox(-1));
    lightboxNext.addEventListener("click",  () => navigateLightbox(1));

    document.addEventListener("keydown", (e: KeyboardEvent) => {
        if (lightbox.classList.contains("hidden")) return;
        if (e.key === "Escape")      closeLightbox();
        if (e.key === "ArrowLeft")   navigateLightbox(-1);
        if (e.key === "ArrowRight")  navigateLightbox(1);
    });

    const nav = document.getElementById("site-nav");
    if (nav) {
        window.addEventListener("scroll", () => {
            nav.style.boxShadow = window.scrollY > 10
                ? "0 4px 24px rgba(0,0,0,0.5)"
                : "none";
        }, { passive: true });
    }
}

// ── Data helpers ───────────────────────────

function flattenImages(data: GalleryData): ImageItem[] {
    const imgs: ImageItem[] = [];
    for (const topic of data.topics) {
        for (const tag of topic.tags) {
            for (const img of tag.images) {
                imgs.push(img);
            }
        }
    }
    return imgs;
}

function sortByCreated(imgs: ImageItem[]): void {
    imgs.sort((a, b) => {
        const aTime = a.created ?? 0;
        const bTime = b.created ?? 0;
        return bTime - aTime;
    });
}

function getTopicImages(data: GalleryData, topicName: string): ImageItem[] {
    const topic = data.topics.find((t) => t.name === topicName);
    if (!topic) return [];
    const imgs: ImageItem[] = [];
    for (const tag of topic.tags) {
        for (const img of tag.images) {
            imgs.push(img);
        }
    }
    return imgs;
}

function getTagsForTopic(data: GalleryData, topicName: string): string[] {
    if (topicName === "ALL") {
        const seen = new Set<string>();
        for (const t of data.topics) {
            for (const tag of t.tags) seen.add(tag.name);
        }
        return Array.from(seen).sort();
    }
    const topic = data.topics.find((t) => t.name === topicName);
    if (!topic) return [];
    return topic.tags.map((t) => t.name);
}

// ── Filters ────────────────────────────────

function applyFilters(data: GalleryData): void {
    let pool: ImageItem[];

    if (currentTopic === "ALL") {
        pool = [...allImages];
    } else {
        pool = getTopicImages(data, currentTopic);
        sortByCreated(pool);
    }

    if (currentTag !== null) {
        pool = pool.filter((img) => img.tag === currentTag);
    }

    filteredImages = pool;
    currentPage    = 1;

    updateCountBadge();
    renderGrid();
    renderPagination();
}

function updateCountBadge(): void {
    const total = filteredImages.length;
    countText.textContent = total + " image" + (total !== 1 ? "s" : "");
}

function renderTagFilters(data: GalleryData): void {
    const tags = getTagsForTopic(data, currentTopic);
    tagFilters.innerHTML = "";
    if (tags.length === 0) return;

    for (const tagName of tags) {
        const btn        = document.createElement("button");
        btn.className    = "tag-btn" + (currentTag === tagName ? " tag-btn--active" : "");
        btn.textContent  = formatTagName(tagName);
        btn.dataset["tag"] = tagName;

        btn.addEventListener("click", () => {
            currentTag  = (currentTag === tagName) ? null : tagName;
            currentPage = 1;
            applyFilters(data);
            document.querySelectorAll<HTMLButtonElement>(".tag-btn").forEach((b) => {
                b.classList.toggle("tag-btn--active", b.dataset["tag"] === currentTag);
            });
        });

        tagFilters.appendChild(btn);
    }
}

function renderGrid(): void {
    const start = (currentPage - 1) * PAGE_SIZE;
    const end   = start + PAGE_SIZE;
    const page  = filteredImages.slice(start, end);

    imageGrid.innerHTML = "";

    if (filteredImages.length === 0) {
        emptyState.classList.remove("hidden");
        pagination.innerHTML = "";
        return;
    }

    emptyState.classList.add("hidden");
    page.forEach((img, i) => imageGrid.appendChild(createImageCard(img, start + i)));
}

function createImageCard(img: ImageItem, globalIndex: number): HTMLElement {
    const card = document.createElement("div");
    card.className = "image-card";
    card.setAttribute("role", "button");
    card.setAttribute("tabindex", "0");

    const picture   = document.createElement("img");
    picture.src     = img.thumbnail;
    picture.alt     = img.filename;
    picture.loading = "lazy";

    card.classList.add("image-skeleton");
    picture.addEventListener("load",  () => card.classList.remove("image-skeleton"));
    picture.addEventListener("error", () => {
        card.classList.remove("image-skeleton");
        picture.src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='400'%3E%3Crect width='400' height='400' fill='%231a1a1a'/%3E%3Ctext x='50%25' y='50%25' font-family='monospace' font-size='12' fill='%23555' text-anchor='middle' dy='.3em'%3ENot%20found%3C/text%3E%3C/svg%3E";
    });

    const meta     = document.createElement("div");
    meta.className = "image-card-meta";
    meta.innerHTML = "<div class=\"image-card-tag\">" + escapeHtml(formatTagName(img.tag)) + "</div>"
                   + "<div class=\"image-card-filename\">" + escapeHtml(img.filename) + "</div>";

    card.appendChild(picture);
    card.appendChild(meta);

    const openFn = () => openLightboxAt(globalIndex);
    card.addEventListener("click", openFn);
    card.addEventListener("keydown", (e: KeyboardEvent) => {
        if (e.key === "Enter" || e.key === " ") { e.preventDefault(); openFn(); }
    });

    return card;
}

// ── Pagination ─────────────────────────────

function renderPagination(): void {
    const totalPages = Math.ceil(filteredImages.length / PAGE_SIZE);
    pagination.innerHTML = "";
    if (totalPages <= 1) return;

    const prev = makePaginationBtn("‹ Prev", currentPage === 1);
    prev.addEventListener("click", () => goToPage(currentPage - 1));
    pagination.appendChild(prev);

    let lastNum = 0;
    for (const num of getPageNumbers(currentPage, totalPages)) {
        if (num - lastNum > 1) {
            const el       = document.createElement("span");
            el.className   = "page-ellipsis";
            el.textContent = "…";
            pagination.appendChild(el);
        }
        const btn = makePaginationBtn(String(num), false);
        if (num === currentPage) btn.classList.add("page-btn--active");
        btn.addEventListener("click", () => goToPage(num));
        pagination.appendChild(btn);
        lastNum = num;
    }

    const next = makePaginationBtn("Next ›", currentPage === totalPages);
    next.addEventListener("click", () => goToPage(currentPage + 1));
    pagination.appendChild(next);
}

function makePaginationBtn(label: string, disabled: boolean): HTMLButtonElement {
    const btn       = document.createElement("button");
    btn.className   = "page-btn";
    btn.textContent = label;
    btn.disabled    = disabled;
    return btn;
}

function getPageNumbers(current: number, total: number): number[] {
    if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);
    const pages = new Set<number>([1, total]);
    for (let i = Math.max(2, current - 2); i <= Math.min(total - 1, current + 2); i++) pages.add(i);
    return Array.from(pages).sort((a, b) => a - b);
}

function goToPage(page: number): void {
    const totalPages = Math.ceil(filteredImages.length / PAGE_SIZE);
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    renderGrid();
    renderPagination();
    window.scrollTo({ top: 0, behavior: "smooth" });
}

// ── Lightbox ───────────────────────────────

function openLightboxAt(index: number): void {
    if (index < 0 || index >= filteredImages.length) return;
    lightboxIndex = index;

    const img       = filteredImages[index];
    lightboxImg.src = "";
    lightboxImg.alt = img.filename;

    lightbox.classList.remove("hidden");
    document.body.style.overflow = "hidden";
    lightboxImg.src = img.full || img.thumbnail;

    const dateStr = img.created ? formatDate(img.created) : "";
    lightboxMeta.innerHTML =
        "<span class=\"lightbox-meta-tag\">" + escapeHtml(img.topic) + " / " + escapeHtml(formatTagName(img.tag)) + "</span>"
      + "<span class=\"lightbox-meta-filename\">" + escapeHtml(img.filename) + "</span>"
      + (dateStr ? "<span class=\"lightbox-meta-date\">" + escapeHtml(dateStr) + "</span>" : "");

    lightboxPrev.style.display = index > 0 ? "" : "none";
    lightboxNext.style.display = index < filteredImages.length - 1 ? "" : "none";
}

function closeLightbox(): void {
    lightbox.classList.add("hidden");
    lightboxImg.src              = "";
    document.body.style.overflow = "";
    lightboxIndex                = -1;
}

function navigateLightbox(direction: -1 | 1): void {
    if (lightboxIndex === -1) return;
    const next = lightboxIndex + direction;
    if (next >= 0 && next < filteredImages.length) openLightboxAt(next);
}

// ── Utilities ──────────────────────────────

function formatTagName(tagName: string): string {
    return tagName.replace(/-/g, " ");
}

function escapeHtml(str: string): string {
    return str
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function formatDate(unixTs: number): string {
    return new Date(unixTs * 1000).toLocaleDateString("en-US", {
        year: "numeric", month: "short", day: "numeric",
    });
}

// ── Bootstrap ──────────────────────────────

document.addEventListener("DOMContentLoaded", init);
