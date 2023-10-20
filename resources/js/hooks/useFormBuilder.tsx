import { create } from 'zustand';
import { arrayMove } from '@dnd-kit/sortable';
import { FieldTypes } from '@/pages/admin/listings/form-builder/components/toolboxItems';

type Fields = {
    id: string;
    type: FieldTypes;
    is_required: boolean;
    options: string[];
    multiple_answer_accepted: boolean;
};

type FormBuilder = {
    active_id: string;
    fields: Fields[];

    append: (fields: Fields) => void;
    remove: (id: string) => void;
    setActive: (id: string) => void;
    swap: (leftIdx: number, rightIdx: number) => void;

    // specific actions
    updateType: (type: FieldTypes, idx: number) => void;
    updateIsRequired: (isRequired: boolean, idx: number) => void;
    updateMultipleAnswerAccepted: (current: boolean, idx: number) => void;
    addOption: (option: string, idx: number) => void;
    removeOption: (idx: number, optionIdx: number) => void;
    updateOption: (option: string, idx: number, optionIdx: number) => void;
};

const useFormBuilder = create<FormBuilder>()((set) => ({
    fields: [],
    active_id: '',
    setActive: (id) => set((state) => ({ ...state, active_id: id })),
    append: (newFields) =>
        set((state) => ({ ...state, fields: [...state.fields, newFields] })),
    remove: (id) =>
        set((state) => ({
            ...state,
            fields: state.fields.filter((field) => field.id !== id),
        })),
    swap: (leftIdx, rightIdx) =>
        set((state) => {
            state.fields = arrayMove(state.fields, leftIdx, rightIdx);
            return state;
        }),

    // specific actions
    updateType: (type, idx) =>
        set((state) => {
            state.fields[idx].type = type;
            return state;
        }),
    updateIsRequired: (isRequired, idx) =>
        set((state) => {
            state.fields[idx].is_required = isRequired;
            return state;
        }),
    updateMultipleAnswerAccepted: (current, idx) =>
        set((state) => {
            state.fields[idx].multiple_answer_accepted = current;
            return state;
        }),

    // options actions
    addOption: (option, idx) =>
        set((state) => {
            state.fields[idx].options.push(option);
            return state;
        }),
    removeOption: (idx, optionIdx) =>
        set((state) => {
            state.fields[idx].options.splice(optionIdx, 1);
            return state;
        }),
    updateOption: (option, idx, optionIdx) =>
        set((state) => {
            state.fields[idx].options[optionIdx] = option;
            return state;
        }),
}));

export default useFormBuilder;
