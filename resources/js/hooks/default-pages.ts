import { v4 as uuidv4 } from 'uuid';
import { Page } from './useFormBuilder';

const defaultPages: Page[] = [
    {
        isDefault: true,
        page_id: uuidv4(),
        fields: [],
        page_title: 'Contact Info',
    },
    {
        isDefault: true,
        page_id: uuidv4(),
        fields: [],
        page_title: 'Search Filter',
    },
];

export default defaultPages;
