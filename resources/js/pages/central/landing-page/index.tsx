import About from './components/About';
import Contact from './components/Contact';
import Download from './components/Download';
import Footer from './components/Footer';
import Header from './components/Header';
import Home from './components/Home';

function LandingPage() {
    return (
        <div className='grid grid-rows-[auto_1fr] gap-y-4'>
            <Header />
            <main className='grid gap-20'>
                <div id='home'>
                    <Home />
                </div>
                <div id='about'>
                    <About />
                </div>
                <div id='download'>
                    <Download />
                </div>
                <div id='contact'>
                    <Contact />
                </div>
            </main>
            <Footer />
        </div>
    );
}

export default LandingPage;
