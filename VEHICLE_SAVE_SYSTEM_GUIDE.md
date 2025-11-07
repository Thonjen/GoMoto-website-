STREAMLINING VEHICLE RENTALS IN SURIGAO CITY THROUGH MOBILE TECHNOLOGY



Capstone Project Presented to 
The Faculty of the College of Computing & Information Sciences
Surigao del Norte State University 
Surigao City






In Partial Fulfillment
Of the Requirements for the Degree
Bachelor of Science in Information Technology




By

Thonjen P. Banguis
Alejandro A. Cayasa


November 2025
STREAMLINING VEHICLE RENTALS IN SURIGAO CITY THROUGH MOBILE TECHNOLOGY
ABSTRACT
This study aimed to develop a mobile-based vehicle rental application called GoMoto to address inefficiencies in Surigao City’s traditional vehicle rental system, which depends on manual transactions, handwritten logs, and inconsistent records. With the city’s growing tourism and increasing demand for accessible transportation, the need for a reliable and centralized rental platform became apparent. The system was designed to provide real-time booking, geolocation-based vehicle listings, and flexible payment options for both renters and vehicle owners. The project utilized the Rapid Application Development (RAD) model, emphasizing iterative prototyping and continuous user feedback to ensure system functionality met local needs. Data collected from surveys and interviews with vehicle owners and renters in Surigao City guided the development process. The prototype was built using Laravel for backend development, MySQL for data management, and React Native for mobile implementation. Testing results demonstrated improvements in booking accuracy, transaction speed, and user convenience. GoMoto streamlined vehicle listing, booking, and payment processes, offering users a secure and transparent rental experience. Overall, the system modernized Surigao City’s vehicle rental operations and supported Surigao del Norte State University’s advocacy for digital innovation and improved community services.


KEYWORDS
android development, mobile application, philippines, vehicle rental, mindanao


1. INTRODUCTION
Surigao City functions as the provincial capital of Surigao del Norte to establish Mindanao's transportation backbone through air and maritime connections to Manila and Cebu and Siargao Island [24]. The Caraga Region welcomed 1,667,504 tourists in 2024 including 1,550,064 domestic visitors and 117,440 international visitors who contributed roughly ₱20 billion to the tourism sector [31]. The increasing number of visitors has created a heightened need for dependable on-demand transportation options that focus on motorcycles and cars because these remain the primary local mobility choices.
The vehicle rental system in Surigao City operates through traditional manual methods despite growing customer demand. The current transaction system in Surigao City relies on informal agreements and social media messaging with handwritten logs which results in multiple booking errors and pricing inconsistencies and incomplete documentation records [28]. The current system's inefficiencies create problems for renters who experience unexpected cancellations or price disputes while vehicle owners must spend excessive time on administrative tasks which degrades service quality and trust.
The digital environment in the Philippines has experienced rapid development simultaneously with other sectors. Internet users reached 85.16 million with 73.1 percent penetration while mobile connections surpassed 168.3 million representing 144.5 percent of the population by early 2023 [27]. The proposed mobile application utilizes existing connectivity to create a centralized system which automates essential vehicle rental operations in Surigao City. Users can search through available motorcycles and cars through the system to make real-time reservations and complete payments at pickup while vehicle owners can manage their listings and track availability and view rental history. The project works to improve operational efficiency and reduce errors while increasing stakeholder satisfaction by addressing manual process challenges and following Surigao del Norte State University's digital innovation vision.

1.1 Research questions:
This study explores the existing challenges faced in the manual motorcycle and car rental processes in Surigao City, particularly focusing on inefficiencies in booking, availability tracking, and communication between renters and vehicle owners. It aims to examine how a mobile-based application can address these issues by streamlining the rental process and improving access to real-time vehicle availability. The research also evaluates the usability and reliability of the proposed system from the perspective of both renters and owners. Furthermore, it assesses the potential impact of the mobile rental platform on overall operational efficiency and stakeholder satisfaction within the local vehicle rental industry.
1.2 Project Context
This project emerged to address Surigao City's disorganized and inefficient traditional vehicle rental system. The project delivers digital solutions that match the community's requirements for modernized local transportation services and convenient operations.
The transportation needs of Surigaonons and visiting tourists depend on motorcycles and cars yet their rental processes currently operate through personal transactions and handwritten logs and social media messaging. The current rental methods create multiple errors while allowing double bookings and fail to establish standard pricing structures and proper documentation. The current system creates difficulties for renters and vehicle providers.
The project supports Surigao del Norte State University's digital transformation goals by using mobile technology to improve local economic activities and transportation systems. The platform serves as a centralized automated system which drives digital solution development that benefits academic communities and the wider city population.


1.3 Purpose and Description
This project develops a mobile application to simplify vehicle rentals including motorcycles and cars throughout Surigao City. Users can access available vehicles and reserve them and pay for them through one unified platform.
Through the application users can manage their bookings alongside viewing vehicle availability and accessing their rental history. The project delivers three essential objectives: error reduction through automation and a user-friendly interface and secure transaction management for the local vehicle rental market.
1.4 General Objectives of the Study
This study aims to design and develop a mobile-based vehicle rental system that will improve rental services in Surigao City.
To gather and analyze relevant requirements, user needs, and current challenges in the vehicle rental process within Surigao City to ensure the system addresses real-world problems and user expectations.
To design and develop a user-friendly mobile application that enables customers to book and manage motorcycle and car rentals, integrates real-time vehicle availability, supports secure payment options, and provides a backend management dashboard for vehicle owners.
To test and evaluate the system's functionality, usability, performance, and overall effectiveness in improving rental operations, ensuring a reliable and efficient platform for both customers and vehicle owners.

1.5 Scope and Limitations
The scope outlines the core functionalities and boundaries of the system, while the limitations identify features and factors excluded from development.
Scope:
The system is intended to offer motorcycle and car rental services exclusively within Surigao City.
The system will support user registration, vehicle browsing, booking requests, rental tracking, and in-app payment.
The system provides a backend interface that allows vehicle owners to manage listings, monitor transactions, and view rental history.
The system integrates real-time geolocation features to help users and owners track vehicle availability and booking activities.
The system will be developed and tested solely on Android devices.
The system includes built-in reports covering total transactions, revenue by owner, cancellation rates, and dispute logs to support project evaluation.
The system incorporates both digital payments via the PayMaya API and a Cash on Delivery (COD) option to cater to diverse user preferences.
The system provides a dashboard and workflows for three user types Customers, Vehicle Owners, and Administrators with role-specific permissions and views.
Limitations:
The system will not support rentals of vans, trucks, or other large vehicles.
The system excludes inter-island or long-distance rental services.
The system does not support features such as ride-hailing, chauffeur services, and vehicle delivery are beyond the project’s focus.
The system relies on mobile geolocation, requiring an active internet connection and location services. As it uses the phone's built-in GPS and data instead of onboard trackers, 
2. REVIEW RELATED LITERATURE
Surigao City serves as the provincial capital of Surigao del Norte and a key transshipment hub linked by airport and seaports to Manila, Cebu, and the neighboring tourism hotspot of Siargao Island [24]. In 2024, the broader Caraga Region welcomed 1,667,504 tourists 1,550,064 domestic and 117,440 foreign visitors yielding roughly ₱20 billion in tourism receipts and underscoring the area’s growing appeal [31]. At the same time, the Philippines’ digital landscape has matured rapidly: by early 2023, there were 85.16 million internet users (73.1 percent penetration) and 168.3 million mobile connections (144.5 percent of the population) [27]. Nationally, the car-rental market was valued at USD 1.34 billion in 2024 and is projected to expand from USD 293 million in 2022 to USD 448.7 million by 2027 at a compound annual growth rate of 9.2 percent [32].
Iterative development and user-centered design emerge as critical success factors across multiple platforms. Semosemo, a React Native prototype developed through requirements gathering, UML modeling, UI/UX design in Figma, implementation in VS Code, and black-box testing, achieved a mean SUS of 82/100 among ten users [10]. Another study using Agile sprints and UML diagrams to integrate live availability and streamline UI flows reported a 25 percent reduction in booking steps and a 15 percent boost in user satisfaction through heuristic evaluations with fifteen participants [5]. Mapping mobile features onto the Business Model Canvas and Balanced Scorecard raised fleet utilization by 12 percent year-over-year and customer retention by 5 percent, underscoring the strategic value of aligning technology design with business KPIs [17].
Collectively, these results suggest that Surigao City’s vehicle‐rental app should prioritize rapid prototyping, continuous usability testing, and a robust yet lightweight back end to meet both user and operational requirements.


Real-time tracking both builds user trust and optimizes operations. One system deployed an Apache Kafka stream handling 10,000 events/sec across 100 vehicles with 99.9 percent uptime and 1.2 s geofence alerts [8]. Arduino/SIM800L/GPS modules transmitting NMEA sentences only when moving cut data costs by 60 percent while maintaining ±2.5 m accuracy [3][11]. Low-cost trackers can achieve ±2.5 m outdoor accuracy, although indoor signal degradation remains a challenge [20]. Fusing GPS, IMU, and LiDAR via deep learning further improves positioning accuracy [12]. Integrated GSM/GPS alerts resolved 95 percent of theft incidents in a three-month trial, demonstrating security benefits beyond mere location reporting [14].
Asset health monitoring via IoT unlocks predictive maintenance and uptime gains. Piloting Azure IoT Hub with LoRaWAN telemetry (fuel, odometer) on sixty vehicles cut unscheduled downtime by 22 percent [9]. Extending IoT to urban mobility smart parking and adaptive signals improved availability by 25 percent with real-time heatmaps [2]. Back-end analytics can trim idle mileage by 8 percent and maintenance costs by 12 percent [16]. 
End-to-end digital workflows drive rapid ROI. ERP migration with mobile access reduced paper handling by 70 percent and cut contract turnaround time from two days to two hours [4]. Ontology-driven business process management yielded a 15 point NPS increase and 35 percent faster e-contract processing [6]. 
Shared-mobility research illuminates user expectations and environmental impacts. Blockchain–IoT car-sharing prototypes can execute tamper-proof smart contracts in under two seconds, suggesting secure frameworks for deposits or insurance [1]. Pooling ten percent of trips cuts CO₂ emissions by eight percent, pointing to group-ride potential [19]. Transparent pricing, SOS features, and reliability emerge as trust drivers for two-wheeled services [7]. Improved accessibility has been noted in Pampanga, though safety and public-transport integration challenges persist [36]. Macro indicators GRDP, labor productivity, and e-hailing use predict motorcycle ownership trends, while cost sensitivity still ties many Metro Manila commuters to traditional modes [22][35].
Local pilots provide grounded insights. Walk-in bookings on Siargao Island experienced an average 45-minute delay due to manual processes [28]. Using Extreme Programming, one prototype achieved 120 ms response times and a speed satisfaction of 4.8/5 [31]. A twenty-vehicle GPS pilot in Davao del Norte scored 4.79/5 for performance and 4.85/5 for trust [21]. Cross-platform designs guided by stakeholder-informed models achieved an 82 percent intent-to-use, and a park-based bike rental feasibility study projected a ₱2.4 million NPV with an 18 percent IRR [26][23][25].
End-user perceptions dictate long-term engagement. SUS benchmarks among 450 TNVS users placed Grab at 95.64, Angkas at 75.88, and JoyRide at 70.26, highlighting “intuitive flow” and “robust payment options” as critical to adoption [29]. To outstrip ride-hailing norms, a vehicle-rental app in Surigao must prioritize streamlined booking flows, transparent transaction feedback, and multiple payment channels.
Macro data confirm fertile ground. Surigao City hosts 1.6 million annual visitors, underscoring an urgent need for modern transport [24]. Sixty percent of Philippine car-rental bookings occur online, with mobile usage rising 8 percent annually amid 73.1 percent internet penetration [32]. Mobile penetration reached 118 percent, and internet users numbered 85.16 million [27]. The domestic car-rental market is projected to grow at 9.2 percent CAGR, from USD 293 million in 2022 to USD 448.7 million by 2027 [30]. Caraga Region tourism receipts totaled ₱20 billion in 2024 [31].
Across eight thematic domains from mobile app design and real-time tracking to IoT maintenance, digital workflows, shared-mobility insights, ethnographic pilots, usability benchmarks, and market data existing scholarship demonstrates feasibility. However, no work integrates these elements into a single, offline-resilient mobile app tailored to Surigao City’s coastal terrain and seasonal tourism cycles. This capstone will bridge that gap by unifying proven methodologies, technologies, and contextual insights into a cohesive, scalable platform.

3. CONCEPTUAL FRAMEWORK










Figure 1. Conceptual Framework
The conceptual framework illustrates the structure of a mobile vehicle rental system designed for Surigao City. The mobile application, developed as a Progressive Web App (PWA) with Tailwind CSS, serves both users and vehicle owners. It integrates mapping and geolocation features using react-native-maps and a Map API while providing secure authentication and real-time database functionality.
The backend, hosted on cPanel, uses PHP and MySQL for data management and custom authentication. Through the Laravel Inertia dashboard, owners can manage their rentals and monitor transactions. Users can also choose between GCash QR code payments and Cash on Delivery as payment options.













Figure 1.1 PWA & Tailwind and Mapping and Geolocation
This part of the framework shows the design and interface of the mobile application, which is developed as a Progressive Web App (PWA) using Tailwind CSS for a clean and responsive layout. The application includes mapping and geolocation features powered by react-native-maps and a Map API, allowing users to view nearby vehicles and rental locations in real time. These components work together to enhance user experience by providing easy navigation, accurate location tracking, and an accessible interface across different devices.













Figure 1.2 PWA & Tailwind and Authentication and Real-Time
This part of the framework illustrates how the Progressive Web App (PWA) developed with Tailwind CSS connects to the authentication and real-time system. The authentication process is handled through a custom setup using MySQL, hosted on cPanel, and managed by a PHP backend server. This ensures secure login, data validation, and continuous communication between the user and the system. The integration allows users to access their accounts, manage data, and perform transactions in real time while maintaining a responsive and user-friendly interface.














Figure 1.3 Authentication and Real-Time and Owner Dashboard - Laravel Inertia
This part of the framework shows the connection between the authentication and real-time system and the Owner Dashboard developed using Laravel Inertia. The dashboard allows vehicle owners to manage their rentals, track bookings, and monitor user activities efficiently. Data from the authentication and real-time system is synchronized with the dashboard, ensuring that all information such as transactions and rental updates is accurate and up to date. This integration provides owners with a seamless management experience and supports efficient system operation.
















Figure 1.4 Authentication and Real-Time and Payment Integration
This part of the framework shows how the authentication and real-time system connects with the payment integration feature. After user verification, the system allows secure and efficient payment processing. Users can choose between GCash QR code payments and Cash on Delivery (COD) options. The real-time connection ensures that all payment transactions are recorded and updated instantly in the system, providing accurate monitoring for both users and vehicle owners. This integration helps maintain secure transactions and improves the overall reliability of the rental process.
3.1 Technical Background
The Vehicle Rental System for Surigao City provides an efficient and modernized platform to simplify motorcycle and car rentals for both residents and visitors. The system is designed as a Progressive Web Application (PWA) developed with Tailwind CSS to ensure a responsive and user-friendly interface that can be accessed on various devices. The mobile application integrates mapping and geolocation features using react-native-maps and a Map API, allowing users to locate nearby vehicles and rental stations with ease. It also includes a custom authentication setup using PHP and MySQL, hosted on cPanel, to manage user credentials and ensure secure access to the system. Real-time data processing allows smooth communication between users, vehicle owners, and the backend server.
The Owner and Admin Dashboard, developed with Laravel Inertia and React, provides a centralized platform for owners to manage rentals, monitor transactions, and handle vehicle listings efficiently. This integration ensures that all updates on bookings and payments are reflected instantly. The system also supports flexible payment methods, offering users the choice between GCash QR code payments and Cash on Delivery (COD). With its secure authentication, real-time synchronization, and geolocation features, the system enhances rental operations and provides a more accessible, reliable, and convenient way to manage vehicle rentals in Surigao City.








3.2 Hardware Specification
Table 1. Hardware Requirements
Component
Requirements
Price
Mobile Device
Android 8.0+ or iOS 12+, minimum 2GB RAM
₱9,000
(Depending on the Brand)
Processor
Intel Core i5 or higher
₱35,000 
RAM
8 - 16 GB minimum
₱4,000 (16 GB)
Storage
256 - 500 GB SSD
₱2,500 (500 GB)
Network
Stable High-Speed Internet Connection
₱1,500 (Per Month)
Total Hardware


₱53,000.00


To support the development and use of the mobile-based vehicle rental system, both mobile and desktop devices with moderate specifications are required. The application runs smoothly on smartphones with at least Android 8.0 or iOS 12 and 2GB of RAM specs common in most modern entry-level devices. On the development side, a laptop with a reliable processor (such as an Intel Core i5), at least 8 to 16 GB of RAM, and SSD storage ensures efficient performance during coding, testing, and design. Stable internet connection is essential for real-time syncing and app functionality. Overall, the hardware requirements are accessible and within range for typical development environments.





3.3 Software Specification
Table 2. Software Requirements
Component
Requirements
Price
Web Server
Apache, Chrome, Firefox, or Safari 
₱2,000.00 (one-time setup)
Database
MySQL
₱2,000.00 
(One-time setup)
Programming Languages
PHP, JavaScript
₱1,000.00 (setup & environment prep)
Frameworks
Laravel (Admin Dashboard), React-Native Expo (User/Owner)
₱3,500.00
Libraries
React Native, tailwind-react-native-classnames, 
₱1,000 (one-time setup estimate)
Testing Method
Manual Testing, real-device testing via Expo Go
₱100 (Data / Load)
Map Services
React-native-maps, Expo Location API
₱500 (setup expenses)
Development Machine
Node.js (v18+), npm or Yarn, Expo CLI,  Visual Studio Code
₱1,000 
(setup & plugin config)
Hosting Platform
Railway
₱280 /month
Operating System
Windows/macOS/Linux,
₱3,199.00 Windows 11 Pro
Sms Notification
Semaphore Sms API
₱0.50 per SMS
Total Software


₱14,579.50

The vehicle rental system uses reliable and budget-friendly tools with a total estimated software cost of ₱14,579.50. It runs on an Apache web server and browsers like Chrome or Firefox, with MySQL handling the database. Development is done in PHP and JavaScript using Laravel for the admin panel and React Native with Expo for the mobile app. Libraries like tailwind-react-native-classnames enhance the UI, while testing is done on real devices via Expo Go. Mapping features use react-native-maps and the Expo Location API. Development setup includes Node.js, npm/Yarn, and Visual Studio Code, with Railway as the hosting platform and Windows 11 Pro as the preferred OS. SMS notifications are sent using Semaphore at ₱0.50 per message.
3.4 Input Process and Output of The System







Figure 2. Input Process and Output of The System
The Vehicle Rental Mobile Application for Surigao City uses mobile technology to improve vehicle rental connections between renters and owners through an enhanced platform. System requirements emerge from research combined with surveys and observations and an extensive evaluation of collected data during the development process. The application development process begins by validating that the app meets genuine requirements of the local community. The system relies on fundamental hardware components such as personal computers, laptops, tablets, and mobile phones while utilizing development tools including Visual Studio Code, GitHub, Expo Go, React Native, JavaScript, Laravel, MySQL, PHP, XAMPP, and Railway. The team uses these technologies to quickly create prototypes and conduct tests and rapid development cycles for system refinement.


The developed system will establish a mobile rental platform for Surigao City vehicles which enables real-time bookings and user-friendly interfaces and ensures secure data management. The system evaluation process includes ongoing assessments of security alongside scalability and reliability and accessibility and user satisfaction and local standards compliance. The systematic approach from data collection through system implementation and assessment guarantees the application provides a contemporary secure rental solution for vehicles which benefits users and owners and enhances citywide operational efficiency.

4. METHODOLOGY
This study proposes to adopt the Rapid Application Development (RAD) model as the development approach for the web-based vehicle rental system. RAD is a user-centered, iterative methodology that emphasizes quick prototyping and regular feedback, making it well-suited for academic projects that aim to build functional systems through ongoing collaboration with stakeholders. The proposed process begins with requirements gathering, which will be conducted through surveys and interviews with vehicle owners and renters in Surigao City. These insights will guide the design of key system features such as booking, mapping and geolocation, authentication, and payment integration. Following this, the team plans to move into the prototyping phase, where early mockups and partial functionalities will be developed and presented to end-users, researchers, and academic advisers for feedback.
Each iteration will refine the prototype based on the collected input, ensuring that the system evolves to meet actual user needs. Planned technologies include a Progressive Web Application (PWA) developed with Tailwind CSS for the interface, react-native-maps and a Map API for geolocation, and a PHP-MySQL backend hosted on cPanel for authentication and real-time data processing. The Owner and Admin Dashboard will be developed using Laravel Inertia and React to provide efficient monitoring and management. The RAD model will guide the project through cycles of design, prototype development, evaluation, and improvement until a stable version suitable for final implementation is reached.






Figure 3. Rapid Application Development (RAD)
4.1. PLANNING REQUIREMENTS
To define the system's core functionalities, the development team conducted interviews and surveys with vehicle renters and owners in Surigao City. These efforts helped identify both functional and non-functional requirements aligned with the community's expectations and addressed shortcomings in the existing manual rental process. The planned mobile reservation system offers real-time access to vehicle availability and booking. Location-based services are integrated using the Expo Location API and react-native-maps, allowing users to view nearby rental vehicles. Secure user authentication is implemented via a custom PHP and MySQL login system. A dedicated Owner Dashboard allows vehicle owners to manage their listings, monitor bookings, and track transactions. 
The system supports flexible payment options, including PayMaya and Cash on Delivery (COD). It is designed with a responsive interface through Expo Go to ensure compatibility with both Android and iOS devices. Real-time data synchronization is also used to prevent double bookings and ensure accuracy. Lastly, the system is built with scalability in mind to support future expansion and maintenance needs.
4.2. PROTOTYPE
The project's prototyping phase required us to create a low-fidelity Figma prototype which displayed the structure and flow of the Vehicle Rental Mobile Application. The initial design phase utilizes wireframes and fundamental interface layouts to create visual representations of essential functionality and user navigation for multiple user types. The prototype features essential screens including Login & Sign Up, HomePage, ViewVehicle, Payment, Profile, OwnerDashboard, and AdminDashboard. 
The screens present planned features and user interactions to enable team feedback collection and development adjustments before starting complete development work. The low-fidelity approach enables flexible design changes because it allows development to proceed without premature commitment to final user interface elements during early stages of application evolution.











Figure 4. Splash Screen













Figure 4.1 Login & Sign Up Page
Users access the application through the Login & Sign Up pages which function as their initial entry points. Users must enter their Email and Password into the Login page's two required fields. Email and Password. Users without an account will receive a prompt to direct them toward the Sign Up page. Users must provide Username, Email Address, First Name, Last Name, Birthdate, Phone Number and Password when creating an account through the Sign Up page. Users who already possess an account can choose to navigate back to the Login page. The basic design approach delivers a simple account setup process that maintains flexibility for future enhancements.


















Figure 4.2 Home Page
The Home Page in Figure 4.2 functions as the main interface for users to explore rental vehicles. The top section contains two elements: a left-aligned app logo and a right-aligned Profile button that enables users to access their settings. Users can find a search bar below the header to filter vehicles while the category selector appears directly beneath it for easy browsing. The main content area presents a scrollable list of vehicles that includes vehicle specs and availability information and pricing details. Users can access different parts of the application through the bottom navigation bar. This low-fidelity prototype establishes the fundamental structure which defines user interactions and visual presentation order.
\
















Figure 4.3 View Vehicle & Confirmation
Users can view rental options on the View Vehicle page before proceeding to the Confirmation page for booking. The View Vehicle screen presents users with two rental plan choices: 12 hours for ₱350 or 6 hours for ₱150. Users can select between a 12-hour rental for ₱350 or a 6-hour rental for ₱150 based on their needs. Users reach the Confirmation page after selecting their plan to complete their booking by verifying payment information and choosing their pick-up and drop-off locations. The user must complete this step to avoid accidental bookings while confirming they have reviewed all booking information. The user journey from vehicle viewing to rental commitment is effectively mapped through the layout's minimal structure.


















Figure 4.4 Profile

The User Profile page in Figure 4.4 allows users to both view and modify their account information. Users can access basic buttons for account detail editing and view past bookings or preferences through this interface. Users can access this section to personalize their account management activities. The User Profile page functions as a basic structure in the low-fidelity prototype to build future design elements while maintaining simplicity for initial testing.




















Figure 4.5 Owner Dashboard
The Owner Dashboard extends the user profile functionality by providing vehicle owners with specialized tools. The screen provides personal information management functions alongside a dashboard and bottom navigation bar which enables owners to see statistics and post listings and access reports. The system design supports owners to track their rental operations effectively. The low-fidelity design enables adaptable improvements throughout the app development cycle and enables better refinement of owner-specific features.







Figure 4.6 Admin Dashboard
The Admin Dashboard in Figure 4.6 serves as the central control interface for user management and vehicle listing administration and system reporting functions. The platform's administrative dashboard enables administrators to monitor all platform operations while performing essential tasks including content moderation and account verification and system-wide data analysis. The current design shows fundamental elements required to preserve application integrity and operational efficiency.







4.3. RECEIVE FEEDBACK
The prototype was evaluated by a group consisting of two IT instructors, one UI/UX designer, and eight student testers. Their feedback highlighted several areas for improvement, such as the need to add confirmation prompts before finalizing bookings and to make pricing details more visible on the View Vehicle screen. They also suggested enhancing the layout of the Owner and Admin dashboards to improve navigation, displaying a summary of user bookings on the Profile page, and including tooltips or brief labels for navigation icons to improve usability.
The feedback sessions showed that users understood the app's essential functions through the wireframes yet they experienced confusion when attempting tasks like vehicle listing editing and user role management. The participants suggested adding visual indicators to distinguish between different user roles such as renter and owner and administrator through dashboard design modifications and color scheme adjustments.
The development team performed successive modifications to the Figma prototype. The development team reorganized dashboard structures to enhance readability while implementing visual indicators for vehicle plan selection and new user guidance elements for the rental workflow. Stakeholder input helped refine the design to meet actual usage needs which enhanced usability before advancing to the development phase.










4.4. FINALIZE SOFTWARE
The finalized version of the mobile application was carefully assembled and tested using Expo Go. On the front end, the development team utilized React Native with JavaScript and TypeScript, styling the interface with tailwind-react-native-classnames and managing app-wide states through the Context API. For the back end, the system was supported by PHP services hosted on Railway and a MySQL database, which handled essential data such as users, vehicles, and reservations. Geolocation features were powered by the Expo Location API and react-native-maps, allowing for dynamic vehicle display and real-time user positioning.
To ensure secure access, the team implemented custom PHP-MySQL login endpoints, enforced HTTPS across all API calls, and incorporated input validation and token-based session management. Payment integration included both digital transactions via the PayMaya API and a Cash on Delivery (COD) option for flexibility. Extensive internal testing was conducted using Android and iOS emulators through Expo Go, alongside real-device tests on compatible phones. These tests focused on verifying core features like booking flows, live map updates, and payment processes. Additional refinements were made based on tester feedback, addressing error handling, edge cases, and overall usability to ensure a polished and reliable user experience.








4.5 EVALUATION METHOD AND TOOLS
To evaluate the proposed system’s overall quality, the team used the ISO 25010 framework, with a focus on key attributes such as usability, reliability, performance efficiency, and security. Usability testing was conducted with a group of 30 renters and 1 vehicle owner to gather diverse user insights. Several evaluation tools were employed during this process. 
The System Usability Scale (SUS), a standardized 10-item questionnaire, was used to measure user satisfaction and ease of use. Task time tracking was implemented to monitor how long participants took to complete essential actions like searching for vehicles, making bookings, and processing payments. An error log captured any system crashes or failures experienced during testing, helping identify technical weaknesses. 
A security checklist and basic penetration testing were carried out to assess the effectiveness of user authentication, data encryption, and the protection of API endpoints. Finally, a post-test feedback form with open-ended questions allowed users to share their satisfaction levels and offer suggestions for improvement.

5. RESULTS AND DISCUSSION
The Vehicle Rental Mobile Application for Surigao City successfully addressed issues in the traditional rental process, such as double bookings and manual errors. Developed using Expo Go and React Native with a Railway-hosted PHP-MySQL backend, the system featured geolocation, real-time bookings, and flexible payment options (PayMaya and COD). Testing showed improved efficiency, reduced transaction errors, and positive user feedback. Vehicle owners benefited from a dashboard for managing listings and transactions. Guided by the Rapid Application Development (RAD) model, the system modernized the local rental process and supported Surigao City’s digital transformation goals.
5.1 Project Planning
	























January (Weeks 1–4)
	The group spent the initial four weeks of January discussing Use Case Diagrams and the Software Development Life Cycle (SDLC), as well as the introduction of the subject course. During this phase, the group identified potential problems to address and received feedback from their adviser to finalize the project topic.
February (Weeks 1–4)
	The group began the Observation and Identification phases in February. During Weeks 1 and 2, the team observed Surigao City’s vehicle rental processes. Weeks 3 and 4 focused on identifying essential requirements and problems from the gathered data before starting the analysis phase.
March (Weeks 1–4)
	In March, the team developed the project’s strategic direction. The Analysis phase began in Week 1, followed by the proposal and finalization of the Project Title during Weeks 2 and 3. By Week 4, the team completed the Research Background and Problem Framing, establishing the key issues the system aimed to solve and forming the basis for the literature review.
April (Weeks 1–4)
	The documentation phase began in April. The first two weeks were dedicated to writing the introduction and reviewing related literature. Week 3 focused on system planning and feasibility studies, while Week 4 introduced the initial system design through diagrams and UI mockups.
May (Weeks 1–4)
	The main focus throughout May was completing documentation and preparing for the title defense. The first week was spent finalizing the system design and drafting the Methodology and Framework sections. Week 2 focused on revising the proposal based on feedback from instructors. By Week 3, the proposal slides were finalized and prepared for presentation.
June to September (Weeks 1–4)
	From June to September, the team focused on the System Development phase. During these months, the system was built and continuously refined based on the conceptual framework. The group also worked on System Documentation and Program Documentation, ensuring that all processes, modules, and technical details were properly recorded. Regular testing and debugging were done to ensure functionality and stability while maintaining progress reports and documentation updates.
October to November (Weeks 1–4)
	During October and November, the team concentrated on System Checkup and the Final Defense preparation. This phase involved final system testing, debugging, and validation to ensure all features were functional and aligned with project requirements. The team also finalized the project documentation and presentation materials in preparation for the final defense.
















5.1.1 Resource Allocation Table

Resource Type
Description
Human Resource
Project Manager, System Analyst, UI/UX Designer, Frontend and Backend Developer, and Quality Assurance Tester – roles shared between two developers
Technical Tools
Android mobile devices and laptops/PCs used for development, testing, and documentation
Infrastructure
-Stable internet connection for development and online testing
-cpanel-hosted MySQL database for production-like data storage and backups
Software Tools
Visual Studio Code,, Git & GitHub, Laravel, PHP, XAMPP, MySQL
Documentation Tools
Google Docs and Microsoft Excel – used for drafting, formatting, collaborating on project documentation, and creating Gantt charts.
Communication Tools
Gmail, Messenger, and  Discord


The development of the Vehicle Rental System for Surigao City requires various resources to ensure an organized and efficient workflow throughout the project. The human resources involved include a Project Manager, System Analyst, UI/UX Designer, Frontend and Backend Developer, and Quality Assurance Tester. These roles are shared between two developers who collaborate to design, develop, test, and document the system. Their combined skills ensure that the project meets both functional and user experience standards. The technical tools used in the project include Android mobile devices and laptops or personal computers, which are essential for system development, testing, and documentation. These devices enable the developers to test the application’s performance across different platforms and screen sizes, ensuring responsiveness and compatibility.
The project also depends on reliable infrastructure, such as a stable internet connection for continuous development and online testing. A cPanel-hosted MySQL database serves as the system’s production-like environment, allowing for real-time data management, backups, and smooth database operations during testing and deployment. For software tools, the development process utilizes Visual Studio Code as the main code editor, along with Git and GitHub for version control and collaboration. Laravel, PHP, XAMPP, and MySQL are used for backend development, database management, and system integration, ensuring a secure and efficient server-side process.
In terms of documentation, Google Docs and Microsoft Excel are used to draft, format, and organize project reports, as well as to create Gantt charts that track progress and timelines. Lastly, communication within the team is maintained through Gmail, Messenger, and Discord, which support efficient coordination, file sharing, and discussions throughout the project’s development cycle.




5.2 Systems Design
		a.) Use-Case Diagram 























Figure 5. Use–Case Diagram
The use case diagram highlights the core CRUD (Create, Read, Update, Delete) functionalities of the vehicle rental mobile application for Surigao City. It illustrates how the three main users Customer, Owner, and Admin interact with the system to manage essential data.
Customers can register and manage their accounts, browse vehicles, make bookings, and cancel them if necessary. These features ensure a smooth and flexible rental experience. Owners are responsible for listing and updating vehicle information, removing listings when needed, and viewing bookings. They can also generate reports to monitor their rental activities and earnings. Admins oversee the entire system. They manage user accounts, assign roles, and maintain records for vehicle types, brands, addresses, and licenses. Admins also have full control over all bookings to ensure proper system management and data accuracy.
This use case diagram reflects a well-structured system that supports efficient data handling, promotes user accountability, and ensures smooth rental operations through clear role-based access.










b.) Class Diagram











Figure 5.1 Class Diagram
The Class Diagram presents the foundational structure of a vehicle rental system, highlighting the key entities and their relationships, each of which supports standard CRUD (Create, Read, Update, Delete) functionalities. At the center of this system is the Users class, representing individuals who may register as renters or vehicle owners. Each user is linked to a specific Role (such as admin, owner, or customer), and may have multiple associated Addresses and Driver_Licenses for identity and eligibility verification. Vehicle ownership is modeled through the Vehicles class, which is directly associated with a user and further categorized using the Vehicle_Types and Brands classes for structured classification and normalization.
Bookings are managed through the Bookings class, linking users to specific vehicles for scheduled rental periods. Each booking can have multiple associated Payments, ensuring accurate tracking of transactions. The diagram uses clear 1-to-1 and 1-to-many relationships to maintain data integrity across users, vehicles, and bookings. This structure supports efficient data operations while keeping the system modular and scalable.
c.) Sequence Diagram















Figure 6 Customer Process Sequence Diagram
This sequence diagram shows how a customer interacts with the system to browse available vehicles, select one for rental, and make a booking. The customer uses the mobile app to fetch the list of vehicles from the vehicle database, selects a vehicle, and sends a booking request. The booking service verifies vehicle availability, then initiates payment processing. Upon successful payment, the booking is confirmed and the customer receives a confirmation message through the app.


















Figure 6.1 Owner Process Sequence Diagram
This diagram illustrates the vehicle owner’s workflow for managing their vehicle listings and monitoring bookings. The owner can add, update, or delete vehicles via the mobile app, which updates the vehicle data in the backend. They can also request to view all bookings related to their vehicles and generate detailed reports summarizing rental activity and earnings. The system fetches booking data and compiles reports to assist owners in managing their rental business efficiently.














Figure 6.2 Admin Process Sequence Diagram

The admin sequence diagram depicts how administrators oversee system-wide management tasks. Admins use an admin panel to perform CRUD operations on user accounts, manage and control bookings, and maintain reference data such as vehicle brands, types, and locations. Each administrative action triggers corresponding updates in the user management system, booking management, or reference database to ensure the integrity and accuracy of the platform.
5.3 System Implementation
The implementation of GoMoto followed a structured and consistent deployment process designed to ensure reliability, maintainability, and accessibility for both users and vehicle owners. The system was hosted on cPanel, which handled essential server tasks such as DNS management, cron scheduling, database setup, and backups through familiar web-based tools. The backend was developed using PHP and Laravel and deployed directly to the cPanel environment for stable performance and easy maintenance. The Progressive Web Application (PWA) was built with Tailwind CSS to provide a responsive and user-friendly interface accessible across devices. Mapping and geolocation services were integrated using react-native-maps and a Map API to assist users in locating nearby vehicles and rental points. 
The Owner and Admin Dashboard, developed with Laravel Inertia and React, supported efficient data synchronization and real-time monitoring of rentals and transactions. Version control and collaboration were managed through GitHub, where updates were tested before deployment. Once verified, the final build was uploaded to the cPanel environment for staging and production, allowing smooth updates, quick fixes, and controlled rollouts during testing and implementation.

Figure 5.3.1 cPanel Deployment Dashboard
Figure 5.3.1 shows a screenshot of the GoMoto application directory within the cPanel File Manager. The screenshot displays the Laravel project structure uploaded to the server. The top-level directories include app, bootstrap, config, database, node_modules, public, resources, routes, storage, tests, and vendor, along with core files such as .env, artisan, composer.json, and .htaccess. The public folder serves as the web root and contains compiled frontend assets, while the storage directory holds uploaded files, cache, and logs. The .env files store runtime configurations and credentials, and the artisan file is used for migrations, maintenance, and scheduled tasks. This setup confirms that the deployed system follows standard Laravel conventions and includes all necessary components for runtime operation.
The backend of GoMoto is designed using a modular architecture that provides RESTful API endpoints for core system operations. The authentication and user management modules handle registration, login, and account validation, while vehicle management endpoints allow owners to add, update, or remove their vehicles. The booking module manages rental transactions, including availability checks, scheduling, and payment status updates. Payment integration supports GCash QR code and Cash on Delivery (COD) options, ensuring flexibility and accessibility for users and vehicle owners.
The database design uses normalized relational tables to maintain system integrity and performance. Key tables include users, vehicles, bookings, payments, and roles, each linked through defined foreign key constraints to ensure consistent relationships between data entities. Laravel migrations were used to create and manage these tables, while Eloquent models define the relationships between users, vehicles, and bookings for smooth CRUD operations.
The GoMoto dashboards provide easy access to essential management tools. The Admin Dashboard allows administrators to manage users, vehicles, and bookings, while the Owner Dashboard enables vehicle owners to monitor their listings, view rental transactions, and track earnings. Both dashboards connect to the same API endpoints as the mobile client, ensuring data consistency across all interfaces.
Testing and quality assurance were conducted throughout the development process using API testing scripts and version control through GitHub. Debugging and error monitoring were supported by Laravel’s built-in logging tools, while cron jobs and scheduled tasks managed system operations like data backups and notifications through cPanel.
The implementation phase also included deployment and maintenance preparations. Environment variables were configured to secure credentials, and HTTPS was enforced through cPanel for data protection. Backup and rollback procedures were documented to ensure system reliability. Overall, the GoMoto system integrates a Laravel backend, a responsive Progressive Web App interface, and cPanel hosting to deliver a secure, maintainable, and production-ready vehicle rental platform for Surigao City.
5.4 Evaluation of the System
The comprehensive evaluation of GoMoto was conducted following its implementation and pilot deployment in Surigao City, using the evaluation methods described in Section 4.5. The system was assessed across several dimensions, including usability, functionality, reliability, performance efficiency, and security, in accordance with the ISO/IEC 25010 software quality standards. Both quantitative data and qualitative feedback were gathered during a four-week testing period involving student testers, vehicle owners, and administrators. Testing environments included a local XAMPP Apache staging server for development validation and the cPanel production host for deployment verification.
Usability evaluation employed the System Usability Scale (SUS) and sprint checklists to measure ease of use and user satisfaction. The results showed a strong positive response, with an average SUS score of 4.5 out of 5. Student testers rated 4.4, while instructor reviewers gave 4.6. Test participants highlighted the system’s simple booking process, clear dashboard layout, and responsive interface as its most user-friendly features. Some suggested minor enhancements to improve navigation on smaller screens and provide clearer visual feedback when submitting booking forms.
Functionality testing confirmed that the core modules of GoMoto operated according to the system requirements. The booking engine correctly verified vehicle availability, enforced owner-defined schedules, and ensured accurate payment tracking. Validation mechanisms effectively prevented duplicate bookings and maintained data integrity across all transactions. Additionally, the integration of GCash QR payments and Cash on Delivery (COD) options was praised for offering flexibility and accessibility to users.
Reliability and performance testing demonstrated that the system handled multiple concurrent transactions without data loss or service interruption. Server response times remained within acceptable limits, and uptime stability was verified through cPanel monitoring tools. The system’s backup procedures and HTTPS encryption further strengthened security and data protection measures. Overall, the evaluation results indicated that GoMoto successfully met its design objectives and provided a reliable, secure, and user-friendly vehicle rental platform suitable for both owners and renters in Surigao City.








5.5 Android Mobile Application











Figure 5.5.1 Landing Page for Mobile Users
This page serves as the main landing screen for new or first-time users. It welcomes them with a title display and a brief description of the application’s purpose. The page also features a browsing section where users can search and explore available vehicles for rent. Additionally, a partnership option is presented below, inviting users to register as vehicle owners and list their vehicles on the platform for rental.















Figure 5.5.2 Browse Vehicle
This page serves as the main interface for users who are looking to rent a vehicle. Here, users can easily browse through the available vehicles and find one that suits their needs. As shown in the image, each vehicle listing displays comprehensive details, including the make and model, features, availability, and rental prices. Users can compare different options at a glance, helping them make an informed decision quickly and conveniently. The layout is designed to be user-friendly, allowing for smooth navigation and a seamless browsing experience, whether the user is looking for a short-term rental or a longer-term option.















Figure 5.5.3 Vehicle Details
This page provides users with comprehensive information about a selected vehicle. As shown in the image, users can view multiple photos of the vehicle, allowing them to examine its condition and features in detail. The page also includes the exact geolocation of the pick-up point, along with instructions on where to make the payment, making the rental process clear and convenient. Additionally, users can read reviews and ratings from previous renters, giving them valuable insights into the vehicle’s performance and the overall rental experience. The design of this page aims to offer transparency and build user confidence, ensuring that they have all the information needed to make a well-informed rental decision.
6. CONCLUSION
This capstone project developed and implemented GoMoto, a vehicle rental management system designed to simplify the process of booking, managing, and tracking vehicle rentals. The project achieved its main objectives by creating an integrated web-based platform for users and vehicle owners, featuring real-time booking, secure payment integration, and administrative dashboards for monitoring transactions and system activity. The system was built using Laravel for the backend with a MySQL database, and deployed in a cPanel-hosted environment to ensure stability, accessibility, and ease of maintenance.
The evaluation of GoMoto showed positive results in usability, system reliability, and efficiency. Testers noted that the platform’s interface was intuitive, and the booking and payment processes were smooth and consistent. The inclusion of GCash QR payments and Cash on Delivery (COD) options improved accessibility for users, while the authentication and dashboard modules enhanced user confidence and transparency.
Furthermore, GoMoto’s database synchronization ensured consistent and accurate updates between users and owners, while the system’s modular Laravel design with migrations, Eloquent models, and organized controllers supported maintainability and scalability. However, the testing phase was limited to a local and small-scale deployment, and further evaluation in a larger production environment is recommended to assess long-term stability and performance under higher user loads.
6.1 Recommendation
Based on the evaluation results and system testing, several recommendations are proposed for the improvement and expansion of GoMoto. First, it is recommended to extend the system’s deployment beyond Surigao City to include nearby areas, allowing a wider range of users and validating the system’s scalability in different network and operational conditions. Second, future developers should conduct performance and security testing in a production-like environment to ensure stable operation during peak booking periods and to safeguard user data and payment transactions.
Third, integrating additional local payment gateways, such as GCash alongside PayMaya, can further improve accessibility and encourage wider user adoption. Fourth, the development of an iOS version is recommended to reach more users and provide cross-platform compatibility. Fifth, enhancing the real-time tracking system with GPS devices or IoT integration could improve accuracy and reliability in vehicle monitoring, especially for rental owners.
Additionally, the system can be improved by implementing advanced analytics and reporting tools to provide deeper insights into rental trends, revenue, and customer behavior. Regular backups, automated maintenance scripts, and system monitoring tools are also encouraged to ensure data safety and continuous service availability. Lastly, a structured user support and onboarding plan, including help documentation and tutorials, should be maintained to assist users and vehicle owners in efficiently navigating the system.

7. ACKNOWLEDGEMENT
The successful completion of this capstone project, GoMoto: Streamlining Vehicle Rentals in Surigao City Through Mobile Technology, would not have been possible without the support, guidance, and encouragement of many individuals and organizations. We, Thonjen P. Banguis and Alejandro A. Cayasa, would like to express our deepest gratitude to all who have contributed to the success of this study.
First and foremost, we extend our heartfelt appreciation to our capstone subject adviser, Ma’am Alma Christie Reyna, for her expertise, guidance, and continuous support throughout the duration of this course. Her insightful comments, constructive feedback, and encouragement helped us refine our research and maintain focus during the most challenging stages of development. Her mentorship has been instrumental in shaping the overall direction and quality of this study.

We would also like to express our sincere gratitude to our project adviser, Engr. Ritchie A. Reyna, for his consistent guidance, valuable suggestions, and technical assistance during the development of this project. His expertise and dedication have greatly influenced the success and completion of this system.
We would like to thank the faculty members of the College of Computing and Information Sciences for imparting the technical knowledge and professional skills that served as the foundation of our project. The lessons we gained from our courses in mobile development, web programming, and database management were essential in building and refining GoMoto.
Our heartfelt thanks also go to the panel of evaluators for their time, feedback, and helpful recommendations during the proposal and final defense. Their insights guided us in improving both the technical aspects and documentation of our project.
Lastly, we are deeply grateful to the individuals and participants in Surigao City who provided their cooperation, feedback, and support during the testing and evaluation phases. Your contributions were vital in refining the system’s features and ensuring its practical use for vehicle rentals. This project is a product of shared effort, knowledge, and encouragement from everyone who believed in our work.
