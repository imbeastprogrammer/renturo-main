import { Input } from '../ui/input';
import { SearchIcon } from 'lucide-react';

function Searchbar() {
    return (
        <div className='relative flex items-center'>
            <SearchIcon className='absolute left-4' />
            <Input placeholder='Search...' className='rounded-full p-6 pl-12' />
        </div>
    );
}

export default Searchbar;
