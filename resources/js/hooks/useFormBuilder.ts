import { create } from 'zustand';
import { v4 as uuidv4 } from 'uuid';
import { persist, createJSONStorage } from 'zustand/middleware';
import { FormElementInstance } from '@/pages/tenants/admin/post-management/dynamic-forms/form-builder/components/FormElement';
import defaultPages from './default-pages';

export type Page = {
    page_title: string;
    page_id: string;
    fields: FormElementInstance[];
    isDefault?: boolean;
};

type FormBuilderState = {
    pages: Page[];
    current_page_id: string | number;
    history: Array<Page[]>;
    future: Array<Page[]>;
};

type FormBuilderAction = {
    setPage: (pageId: string | number) => void;
    addPage: () => void;
    setPages: (pages: Page[]) => void;
    updatePage: (pageId: string | number, page: Page) => void;
    removePage: (pageId: string | number) => void;
    setFields: (pageId: string | number, fields: FormElementInstance[]) => void;
    addField: (
        pageId: string | number,
        index: number,
        field: FormElementInstance,
    ) => void;
    removeField: (pageId: string | number, id: string | number) => void;
    selectedField: FormElementInstance | null;
    setSelectedField: (field: FormElementInstance) => void;
    updateField: (
        pageId: string | number,
        id: string | number,
        field: FormElementInstance,
    ) => void;
    undo: () => void;
    redo: () => void;
};

type FormbuilderStore = FormBuilderState & FormBuilderAction;

const HISTORY_LIMIT = 20;

const useFormBuilder = create<FormbuilderStore>()(
    persist(
        (set) => ({
            pages: defaultPages,
            current_page_id: defaultPages[0].page_id,
            selectedField: null,
            history: [],
            future: [],
            setPage: (pageId) => set({ current_page_id: pageId }),
            setPages: (pages) => set({ pages }),
            addPage: () =>
                set((state) => {
                    const history =
                        state.history.length >= HISTORY_LIMIT
                            ? [...state.history.slice(1, 50), state.pages]
                            : [...state.history, state.pages];

                    return {
                        ...state,
                        history,
                        pages: [
                            ...state.pages,
                            { page_id: uuidv4(), page_title: '', fields: [] },
                        ],
                    };
                }),
            updatePage: (pageId, newPage) =>
                set((state) => {
                    const pageUpdated = state.pages.map((page) =>
                        page.page_id === pageId ? newPage : page,
                    );

                    const history =
                        state.history.length >= HISTORY_LIMIT
                            ? [...state.history.slice(1, 50), state.pages]
                            : [...state.history, state.pages];

                    return {
                        ...state,
                        history,
                        pages: pageUpdated,
                    };
                }),
            removePage: (pageId) =>
                set((state) => {
                    if (state.current_page_id === pageId)
                        throw new Error('You cannot delete a active page');

                    const pageToDelete = state.pages.find(
                        (page) => page.page_id === pageId,
                    );

                    if (pageToDelete?.isDefault)
                        throw new Error('You cannot delete a default page');

                    const pageRemoved = state.pages.filter(
                        (page) => page.page_id !== pageId,
                    );

                    const history =
                        state.history.length >= HISTORY_LIMIT
                            ? [...state.history.slice(1, 50), state.pages]
                            : [...state.history, state.pages];

                    return {
                        ...state,
                        history,
                        pages: pageRemoved,
                    };
                }),
            setFields: (pageId, fields) =>
                set((state) => {
                    const pageUpdated = state.pages.map((page) =>
                        page.page_id === pageId ? { ...page, fields } : page,
                    );

                    const history =
                        state.history.length >= HISTORY_LIMIT
                            ? [...state.history.slice(1, 50), state.pages]
                            : [...state.history, state.pages];

                    return {
                        ...state,
                        history,
                        pages: pageUpdated,
                    };
                }),
            addField: (pageId, index, field) =>
                set((state) => {
                    const pageUpdated = state.pages.map((page) => {
                        if (page.page_id === pageId) {
                            const prevFields = [...page.fields];
                            prevFields.splice(index, 0, field);
                            return { ...page, fields: prevFields };
                        }
                        return page;
                    });

                    const history =
                        state.history.length >= HISTORY_LIMIT
                            ? [...state.history.slice(1, 50), state.pages]
                            : [...state.history, state.pages];

                    return {
                        ...state,
                        history,
                        pages: pageUpdated,
                    };
                }),
            removeField: (pageId, id) =>
                set((state) => {
                    const pageUpdated = state.pages.map((page) => {
                        if (page.page_id === pageId) {
                            const fieldRemoved = page.fields.filter(
                                (field) => field.id !== id,
                            );
                            return { ...page, fields: fieldRemoved };
                        }
                        return page;
                    });

                    const history =
                        state.history.length >= HISTORY_LIMIT
                            ? [...state.history.slice(1, 50), state.pages]
                            : [...state.history, state.pages];

                    return {
                        ...state,
                        history,
                        pages: pageUpdated,
                    };
                }),
            setSelectedField: (field) => set({ selectedField: field }),
            updateField: (pageId, id, updatedField) =>
                set((state) => {
                    const pageUpdated = state.pages.map((page) => {
                        if (page.page_id === pageId) {
                            const fieldUpdated = page.fields.map((field) =>
                                field.id === id ? updatedField : field,
                            );
                            return { ...page, fields: fieldUpdated };
                        }
                        return page;
                    });

                    const history =
                        state.history.length >= HISTORY_LIMIT
                            ? [...state.history.slice(1, 50), state.pages]
                            : [...state.history, state.pages];

                    return {
                        ...state,
                        history,
                        pages: pageUpdated,
                    };
                }),
            undo: () =>
                set((state) => {
                    const { pages: currentPages, history, future } = state;
                    if (history.length === 0) return state;
                    const previousState = history[history.length - 1];
                    const newHistory = [...history.slice(0, -1)];

                    return {
                        ...state,
                        pages: previousState,
                        history: newHistory,
                        future: [...future, currentPages],
                    };
                }),
            redo: () =>
                set((state) => {
                    const { history, pages, future } = state;
                    if (future.length === 0) return state;
                    const nextState = future[future.length - 1];
                    const newFuture = [...future.slice(0, -1)];

                    return {
                        ...state,
                        pages: nextState,
                        future: newFuture,
                        history: [...history, pages],
                    };
                }),
        }),
        {
            name: 'form-builder-storage',
            storage: createJSONStorage(() => localStorage),
        },
    ),
);

export default useFormBuilder;
