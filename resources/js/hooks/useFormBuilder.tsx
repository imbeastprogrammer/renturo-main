import { create } from 'zustand';
import { persist, createJSONStorage } from 'zustand/middleware';
import { FormElementInstance } from '@/pages/admin/listings/form-builder/components/FormElement';

type FormBuilder = {
    fields: FormElementInstance[];
    setFields: (fields: FormElementInstance[]) => void;
    addField: (index: number, field: FormElementInstance) => void;
    removeField: (id: string) => void;

    selectedField: FormElementInstance | null;
    setSelectedField: (field: FormElementInstance) => void;
    updateField: (id: string, field: FormElementInstance) => void;
};

const useFormBuilder = create<FormBuilder>()(
    persist(
        (set) => ({
            fields: [],
            selectedField: null,
            setFields: (fields) => set({ fields }),
            addField: (index, field) =>
                set((state) => {
                    const prev = [...state.fields];
                    prev.splice(index, 0, field);
                    return { ...state, fields: prev };
                }),
            removeField: (id) =>
                set((state) => {
                    const newFields = state.fields.filter(
                        (field) => field.id !== id,
                    );
                    return { ...state, fields: newFields };
                }),
            setSelectedField: (field) => set({ selectedField: field }),
            updateField: (id, field) =>
                set((state) => {
                    const updated = state.fields.map((curr) =>
                        curr.id === id ? field : curr,
                    );
                    return { ...state, fields: updated };
                }),
        }),
        {
            name: 'form-builder-storage',
            storage: createJSONStorage(() => sessionStorage),
        },
    ),
);

export default useFormBuilder;
