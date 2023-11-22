import { create } from 'zustand';
import { v4 as uuidv4 } from 'uuid';
import { persist, createJSONStorage } from 'zustand/middleware';
import { FormElementInstance } from '@/pages/tenants/admin/post-management/form-builder/components/FormElement';

type Page = {
    page_title: string;
    page_id: string;
    fields: FormElementInstance[];
};

type FormBuilderState = {
    pages: Page[];
    current_page_id: string;
    history: Array<Page[]>;
    future: Array<Page[]>;
};

type FormBuilderAction = {
    setPage: (pageId: string) => void;
    addPage: () => void;
    setPages: (pages: Page[]) => void;
    updatePage: (pageId: string, page: Page) => void;
    removePage: (pageId: string) => void;
    setFields: (pageId: string, fields: FormElementInstance[]) => void;
    addField: (
        pageId: string,
        index: number,
        field: FormElementInstance,
    ) => void;
    removeField: (pageId: string, id: string) => void;
    selectedField: FormElementInstance | null;
    setSelectedField: (field: FormElementInstance) => void;
    updateField: (
        pageId: string,
        id: string,
        field: FormElementInstance,
    ) => void;
    undo: () => void;
    redo: () => void;
};

type FormbuilderStore = FormBuilderState & FormBuilderAction;

const defaultPage: Page = {
    page_title: '',
    page_id: uuidv4(),
    fields: [],
};

const useFormBuilder = create<FormbuilderStore>()(
    persist(
        (set) => ({
            pages: [defaultPage],
            current_page_id: defaultPage.page_id,
            selectedField: null,
            history: [],
            future: [],
            setPage: (pageId) => set({ current_page_id: pageId }),
            setPages: (pages) => set({ pages }),
            addPage: () =>
                set((state) => ({
                    ...state,
                    history: [...state.history, state.pages],
                    pages: [
                        ...state.pages,
                        { page_id: uuidv4(), page_title: '', fields: [] },
                    ],
                })),
            updatePage: (pageId, newPage) =>
                set((state) => {
                    const pageUpdated = state.pages.map((page) =>
                        page.page_id === pageId ? newPage : page,
                    );
                    return {
                        ...state,
                        history: [...state.history, state.pages],
                        pages: pageUpdated,
                    };
                }),
            removePage: (pageId) =>
                set((state) => {
                    if (state.current_page_id === pageId)
                        throw new Error('You cannot delete a active page');
                    const pageRemoved = state.pages.filter(
                        (page) => page.page_id !== pageId,
                    );
                    return {
                        ...state,
                        history: [...state.history, state.pages],
                        pages: pageRemoved,
                    };
                }),
            setFields: (pageId, fields) =>
                set((state) => {
                    const pageUpdated = state.pages.map((page) =>
                        page.page_id === pageId ? { ...page, fields } : page,
                    );
                    return {
                        ...state,
                        history: [...state.history, state.pages],
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
                    return {
                        ...state,
                        history: [...state.history, state.pages],
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
                    return {
                        ...state,
                        history: [...state.history, state.pages],
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
                    return {
                        ...state,
                        history: [...state.history, state.pages],
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
