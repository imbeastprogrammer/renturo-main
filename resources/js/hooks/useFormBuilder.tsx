import { create } from 'zustand';
import { v4 as uuidv4 } from 'uuid';
import { persist, createJSONStorage } from 'zustand/middleware';
import { FormElementInstance } from '@/pages/admin/listings/form-builder/components/FormElement';

type Page = {
    page_title: string;
    page_id: string;
    fields: FormElementInstance[];
};

type FormBuilder = {
    pages: Page[];
    current_page_id: string;
    setPage: (pageId: string) => void;
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
};

const defaultPage: Page = {
    page_title: '',
    page_id: uuidv4(),
    fields: [],
};

const useFormBuilder = create<FormBuilder>()(
    persist(
        (set) => ({
            pages: [defaultPage],
            current_page_id: defaultPage.page_id,
            selectedField: null,
            setPage: (pageId) => set({ current_page_id: pageId }),
            removePage: (pageId) =>
                set((state) => {
                    const pageRemoved = state.pages.filter(
                        (page) => page.page_id !== pageId,
                    );
                    return {
                        ...state,
                        pages: pageRemoved,
                    };
                }),
            setFields: (pageId, fields) =>
                set((state) => {
                    const pageUpdated = state.pages.map((page) =>
                        page.page_id === pageId ? { ...page, fields } : page,
                    );
                    return { ...state, pages: pageUpdated };
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
                    return { ...state, pages: pageUpdated };
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
                    return { ...state, pages: pageUpdated };
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
                    return { ...state, pages: pageUpdated };
                }),
        }),
        {
            name: 'form-builder-storage',
            storage: createJSONStorage(() => sessionStorage),
        },
    ),
);

export default useFormBuilder;
