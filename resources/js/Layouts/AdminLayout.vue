<script setup lang="ts">
import { Link, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const open = ref(false);
const page = usePage();
const user = computed(() => page.props.auth?.user as any);
const currentSection = computed(
    () =>
        navigation.find((item) => route().current(item.match))?.label ||
        "Admin",
);
const navigation = [
    {
        label: "Dashboard",
        href: "/admin/dashboard",
        icon: "grid",
        match: "admin.dashboard",
    },
    {
        label: "Submission",
        href: "/admin/submissions",
        icon: "inbox",
        match: "admin.submissions.*",
    },
    {
        label: "Pesan Terkirim",
        href: "/admin/messages",
        icon: "mail",
        match: "admin.messages.*",
    },
    {
        label: "Data Terhapus",
        href: "/admin/trash",
        icon: "trash",
        match: "admin.trash.*",
    },
    {
        label: "FAQ",
        href: "/admin/faqs",
        icon: "question",
        match: "admin.faqs.*",
    },
    {
        label: "Setting",
        href: "/admin/settings",
        icon: "settings",
        match: "admin.settings.*",
        superAdmin: true,
    },
];
</script>

<template>
    <div class="admin-shell">
        <button
            class="mobile-trigger"
            type="button"
            aria-label="Buka menu admin"
            @click="open = !open"
        >
            <span></span><span></span><span></span>
        </button>
        <aside :class="['sidebar', { open }]">
            <Link href="/admin/dashboard" class="brand" @click="open = false">
                <span class="brand-mark">OS</span>
                <span><b>ORIGINAL</b><small>SESSIONS 2026</small></span>
            </Link>
            <nav>
                <Link
                    v-for="item in navigation.filter(
                        (item) =>
                            !item.superAdmin || user?.role === 'super_admin',
                    )"
                    :key="item.href"
                    :href="item.href"
                    :class="{ active: route().current(item.match) }"
                    @click="open = false"
                >
                    <svg v-if="item.icon === 'grid'" viewBox="0 0 24 24">
                        <path
                            d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"
                        />
                    </svg>
                    <svg v-else-if="item.icon === 'inbox'" viewBox="0 0 24 24">
                        <path d="M4 5h16v14H4zM4 14h4l2 2h4l2-2h4" />
                    </svg>
                    <svg v-else-if="item.icon === 'mail'" viewBox="0 0 24 24">
                        <path d="M3 5h18v14H3zM3 7l9 7 9-7" />
                    </svg>
                    <svg v-else-if="item.icon === 'trash'" viewBox="0 0 24 24">
                        <path d="M4 7h16M9 7V4h6v3m3 0-1 14H7L6 7m4 4v6m4-6v6" />
                    </svg>
                    <svg v-else-if="item.icon === 'star'" viewBox="0 0 24 24">
                        <path
                            d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1-4.4-4.3 6.1-.9z"
                        />
                    </svg>
                    <svg
                        v-else-if="item.icon === 'calendar'"
                        viewBox="0 0 24 24"
                    >
                        <path d="M5 5h14v15H5zM8 3v4m8-4v4M5 10h14" />
                    </svg>
                    <svg
                        v-else-if="item.icon === 'document'"
                        viewBox="0 0 24 24"
                    >
                        <path d="M6 3h9l3 3v15H6zM9 11h6M9 15h6" />
                    </svg>
                    <svg
                        v-else-if="item.icon === 'question'"
                        viewBox="0 0 24 24"
                    >
                        <path
                            d="M9.5 9a2.5 2.5 0 1 1 3.5 2.3c-1 .4-1 1.1-1 2.2M12 18h.01"
                        />
                    </svg>
                    <svg v-else viewBox="0 0 24 24">
                        <path
                            d="M12 8.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7zM19 13.5l2-1.5-2-1.5-.6-1.5.4-2.4-2.4-.4L15 4l-2 1h-2L9 4 7.6 6.2l-2.4.4.4 2.4L5 10.5 3 12l2 1.5.6 1.5-.4 2.4 2.4.4L9 20l2-1h2l2 1 1.4-2.2 2.4-.4-.4-2.4z"
                        />
                    </svg>
                    {{ item.label }}
                </Link>
            </nav>
            <div class="account">
                <span class="avatar">{{ user?.name?.charAt(0) || "A" }}</span>
                <div>
                    <b>{{ user?.name }}</b
                    ><small>{{
                        String(user?.role || "").replaceAll("_", " ")
                    }}</small>
                </div>
            </div>
            <Link href="/logout" method="post" as="button" class="logout"
                >← Keluar</Link
            >
        </aside>
        <button
            v-if="open"
            class="backdrop"
            aria-label="Tutup menu"
            @click="open = false"
        ></button>
        <main class="admin-content">
            <header class="admin-header">
                <div>
                    <small>ORIGINAL SESSIONS 2026</small
                    ><b>{{ currentSection }}</b>
                </div>
                <div class="header-user">
                    <span>{{ user?.username }}</span
                    ><i></i><span>{{ user?.name }}</span>
                </div>
            </header>
            <div class="admin-page"><slot /></div>
            <footer class="admin-footer">
                <span>© 2026 Original Sessions</span
                ><span>Admin Panel · SoundFresh.id</span>
            </footer>
        </main>
    </div>
</template>

<style scoped>
.admin-shell {
    min-height: 100vh;
    background: #080d19;
    color: #edf2f7;
}
.sidebar {
    position: fixed;
    inset: 0 auto 0 0;
    z-index: 30;
    display: flex;
    width: 280px;
    flex-direction: column;
    border-right: 1px solid #202a3b;
    background: #101827;
    padding: 28px 18px;
}
.brand {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0 12px 28px;
}
.brand-mark {
    display: grid;
    width: 44px;
    height: 44px;
    place-items: center;
    border-radius: 14px;
    background: linear-gradient(145deg, #ff822e, #e44816);
    box-shadow: 0 10px 30px #ff6a0033;
    font-family: "Space Grotesk";
    font-weight: 800;
    color: #0b101c;
}
.brand b,
.brand small {
    display: block;
}
.brand b {
    letter-spacing: 0.12em;
}
.brand small {
    margin-top: 2px;
    color: #ff8336;
    font-size: 10px;
    letter-spacing: 0.16em;
}
.sidebar nav {
    display: grid;
    gap: 7px;
}
.sidebar nav a {
    display: flex;
    align-items: center;
    gap: 13px;
    border-radius: 14px;
    padding: 13px 14px;
    color: #98a5b8;
    font-size: 14px;
    font-weight: 600;
    transition: 0.18s;
}
.sidebar nav a:hover {
    background: #172133;
    color: white;
}
.sidebar nav a.active {
    background: linear-gradient(90deg, #3b1b22, #291823);
    color: #ff9a5b;
}
.sidebar svg {
    width: 19px;
    height: 19px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.7;
}
.sidebar nav a:first-child svg {
    fill: currentColor;
    stroke: none;
}
.account {
    display: grid;
    grid-template-columns: 42px 1fr;
    align-items: center;
    gap: 11px;
    margin-top: auto;
    border-top: 1px solid #263044;
    padding: 22px 10px 12px;
}
.avatar {
    display: grid;
    width: 40px;
    height: 40px;
    place-items: center;
    border-radius: 50%;
    background: #4d1b24;
    color: #ffb28a;
    font-weight: 800;
}
.account b,
.account small {
    display: block;
    text-transform: capitalize;
}
.account small {
    margin-top: 3px;
    color: #728096;
    font-size: 12px;
}
.logout {
    padding: 10px;
    color: #ff7b65;
    text-align: left;
    font-size: 13px;
}
.admin-content {
    min-height: 100vh;
    margin-left: 280px;
    padding: 36px;
}
.mobile-trigger,
.backdrop {
    display: none;
}
@media (max-width: 850px) {
    .sidebar {
        transform: translateX(-100%);
        transition: 0.25s;
    }
    .sidebar.open {
        transform: none;
    }
    .admin-content {
        margin-left: 0;
        padding: 74px 18px 24px;
    }
    .mobile-trigger {
        position: fixed;
        top: 18px;
        left: 18px;
        z-index: 40;
        display: grid;
        width: 42px;
        height: 42px;
        place-content: center;
        gap: 4px;
        border: 1px solid #28344a;
        border-radius: 12px;
        background: #111827;
    }
    .mobile-trigger span {
        display: block;
        width: 18px;
        height: 2px;
        background: #f2f5f9;
    }
    .backdrop {
        position: fixed;
        inset: 0;
        z-index: 20;
        display: block;
        background: #030712aa;
    }
}
</style>
<style scoped>
.admin-content {
    display: flex;
    padding: 0;
    flex-direction: column;
}
.admin-header {
    position: sticky;
    top: 0;
    z-index: 10;
    display: flex;
    min-height: 72px;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #202a3b;
    background: #080d19e8;
    padding: 0 36px;
    backdrop-filter: blur(14px);
}
.admin-header small,
.admin-header b {
    display: block;
}
.admin-header small {
    color: #ff7d2d;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 0.18em;
}
.admin-header b {
    margin-top: 4px;
    font-size: 14px;
}
.header-user {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #7e8ba0;
    font-size: 11px;
}
.header-user i {
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: #ff6a00;
}
.admin-page {
    flex: 1;
    padding: 32px 36px;
}
.admin-footer {
    display: flex;
    justify-content: space-between;
    border-top: 1px solid #202a3b;
    padding: 20px 36px;
    color: #59667b;
    font-size: 10px;
}
@media (max-width: 850px) {
    .admin-content {
        padding: 0;
    }
    .admin-header {
        padding: 0 18px 0 72px;
    }
    .header-user span:first-child,
    .header-user i {
        display: none;
    }
    .admin-page {
        padding: 24px 18px;
    }
    .admin-footer {
        align-items: center;
        gap: 6px;
        flex-direction: column;
        padding: 18px;
    }
}
.admin-page :deep(.bg-neutral-100) {
    color: #171717;
}
.admin-page :deep(.min-h-screen.bg-neutral-100) {
    min-height: auto;
    border-radius: 18px;
}
</style>
