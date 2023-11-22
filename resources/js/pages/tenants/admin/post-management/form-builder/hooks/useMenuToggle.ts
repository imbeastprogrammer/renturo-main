import { create } from 'zustand';

type MenuToggle = {
    isOpen: boolean;
    toggleMenu: (isOpen: boolean) => void;
};

const useMenuToggle = create<MenuToggle>()((set) => ({
    isOpen: false,
    toggleMenu: (isOpen) => set({ isOpen: !isOpen }),
}));

export default useMenuToggle;
