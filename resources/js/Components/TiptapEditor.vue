<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import { watch } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);

const editor = useEditor({
    content: props.modelValue,
    extensions: [StarterKit],
    editorProps: {
        attributes: {
            class: 'prose prose-sm max-w-none focus:outline-none min-h-[120px] px-4 py-3',
        },
    },
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML());
    },
});

watch(() => props.modelValue, (val) => {
    if (editor.value && editor.value.getHTML() !== val) {
        editor.value.commands.setContent(val, false);
    }
});
</script>

<template>
    <div class="editor-wrapper">
        <div v-if="editor" class="toolbar">
            <button type="button" @click="editor.chain().focus().toggleBold().run()" :class="{ active: editor.isActive('bold') }">
                <strong>B</strong>
            </button>
            <button type="button" @click="editor.chain().focus().toggleItalic().run()" :class="{ active: editor.isActive('italic') }">
                <em>I</em>
            </button>
            <span class="divider"></span>
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()" :class="{ active: editor.isActive('heading', { level: 3 }) }">
                H3
            </button>
            <button type="button" @click="editor.chain().focus().toggleBulletList().run()" :class="{ active: editor.isActive('bulletList') }">
                •&nbsp;List
            </button>
            <button type="button" @click="editor.chain().focus().toggleOrderedList().run()" :class="{ active: editor.isActive('orderedList') }">
                1.&nbsp;List
            </button>
            <span class="divider"></span>
            <button type="button" @click="editor.chain().focus().toggleBlockquote().run()" :class="{ active: editor.isActive('blockquote') }">
                Quote
            </button>
        </div>
        <EditorContent :editor="editor" />
    </div>
</template>

<style scoped>
.editor-wrapper {
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    background: #f9fafb;
    overflow: hidden;
    transition: all 0.2s;
}

.editor-wrapper:focus-within {
    border-color: #6366f1;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
}

.toolbar {
    display: flex;
    align-items: center;
    gap: 2px;
    padding: 0.375rem 0.5rem;
    border-bottom: 1px solid #f3f4f6;
    background: #fafafa;
}

.toolbar button {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 500;
    color: #6b7280;
    border: none;
    background: none;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: all 0.15s;
}

.toolbar button:hover {
    background: #e5e7eb;
    color: #111827;
}

.toolbar button.active {
    background: #6366f1;
    color: #fff;
}

.divider {
    width: 1px;
    height: 1rem;
    background: #e5e7eb;
    margin: 0 0.25rem;
}
</style>
